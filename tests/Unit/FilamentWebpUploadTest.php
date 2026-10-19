<?php

namespace Tests\Unit;

use App\Models\Offer;
use App\Support\FilamentWebpUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Tests\TestCase;

class FilamentWebpUploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (! function_exists('imagewebp')) {
            $this->markTestSkipped('GD WebP support is required.');
        }
        Storage::fake('public');
    }

    private function source(string $path, int $width, int $height): void
    {
        $disk = Storage::disk('public');
        $disk->makeDirectory(dirname($path));
        $image = imagecreatetruecolor($width, $height);
        // Fine texture provides a meaningful compression/resampling fixture.
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                imagesetpixel($image, $x, $y, (($x * 17 + $y * 13) % 256) << 16 | (($x * 3 + $y * 7) % 256) << 8 | (($x + $y * 5) % 256));
            }
        }
        imagewebp($image, $disk->path($path), 82);
        imagedestroy($image);
    }

    public function test_variants_preserve_aspect_ratio_primary_bytes_and_existing_derivatives(): void
    {
        $primary = 'offers/hero-mobile/phase-one.webp';
        $this->source($primary, 1200, 900);
        $disk = Storage::disk('public');
        $original = $disk->get($primary);
        $paths = FilamentWebpUpload::generateResponsiveVariants($primary);

        $this->assertSame([480, 800, 1200], array_keys($paths));
        foreach ($paths as $width => $path) {
            $info = getimagesize($disk->path($path));
            $this->assertSame($width, $info[0]);
            $this->assertSame((int) ($width * 0.75), $info[1]);
            $this->assertSame('image/webp', $info['mime']);
            $this->assertLessThan(strlen($original), $disk->size($path));
            $this->assertStringEndsWith('/'.$path, $disk->url($path));
            fwrite(STDOUT, "\n{$path}: {$info[0]}x{$info[1]}, ".$disk->size($path).' bytes');
        }
        fwrite(STDOUT, "\nPrimary: 1200x900, ".strlen($original)." bytes\n");
        $hashes = array_map(fn ($path) => hash('sha256', $disk->get($path)), $paths);
        $this->assertSame($paths, FilamentWebpUpload::generateResponsiveVariants($primary));
        $this->assertSame($hashes, array_map(fn ($path) => hash('sha256', $disk->get($path)), $paths));
        $this->assertSame($original, $disk->get($primary));
    }

    public function test_small_non_four_by_three_sources_are_not_upscaled_or_duplicated(): void
    {
        $primary = 'offers/hero-mobile/small.webp';
        $this->source($primary, 600, 300);
        $paths = FilamentWebpUpload::generateResponsiveVariants($primary);
        $this->assertSame([480, 600], array_keys($paths));
        foreach ($paths as $width => $path) {
            $info = getimagesize(Storage::disk('public')->path($path));
            $this->assertSame([$width, (int) ($width / 2)], array_slice($info, 0, 2));
        }
        $this->assertCount(3, Storage::disk('public')->files('offers/hero-mobile'));
    }

    public function test_variants_use_primary_crop_and_ignore_legacy_uncropped_variants(): void
    {
        $primary = 'offers/hero-mobile/cropped.webp';
        $source = 'fixtures/uncropped.webp';
        $this->source($primary, 1200, 900);
        $this->source($source, 1000, 500);
        Storage::disk('public')->put('offers/hero-mobile/cropped-w480.webp', Storage::disk('public')->get($source));
        $paths = FilamentWebpUpload::generateResponsiveVariants($primary);
        $this->assertSame([480, 800, 1200], array_keys($paths));
        foreach ($paths as $width => $path) {
            $info = getimagesize(Storage::disk('public')->path($path));
            $this->assertSame([$width, (int) ($width * 0.75)], array_slice($info, 0, 2));
        }
        $this->assertStringNotContainsString('cropped-w480.webp', FilamentWebpUpload::responsiveSrcset($primary));
    }

    public function test_upload_opt_in_keeps_primary_processing_and_avoids_filename_collisions(): void
    {
        config(['livewire.temporary_file_upload.directory' => 'fixtures']);
        $this->source('fixtures/upload.webp', 600, 300);
        $file = new TemporaryUploadedFile('upload.webp', 'public');
        $disk = Storage::disk('public');
        $disk->makeDirectory('offers/hero-mobile');
        $disk->put('offers/hero-mobile/test-w480.webp', 'reserved');

        $path = FilamentWebpUpload::store($file, 'offers/hero-mobile', 1200, 900, 'test', responsiveVariants: true);
        $this->assertSame('offers/hero-mobile/test-2.webp', $path);
        $this->assertSame([1200, 900], array_slice(getimagesize($disk->path($path)), 0, 2));
        $this->assertSame([400, 300], array_slice(getimagesize($disk->path('offers/hero-mobile/test-2-crop-w400.webp')), 0, 2));
        $this->assertSame('reserved', $disk->get('offers/hero-mobile/test-w480.webp'));
        $this->assertStringContainsString('test-2-crop-w400.webp 400w', FilamentWebpUpload::responsiveSrcset($path));

        $plain = FilamentWebpUpload::store($file, 'offers/hero-mobile', 1200, 900, 'plain');
        $this->assertSame($disk->get($plain), $disk->get($path));
        $disk->assertMissing('offers/hero-mobile/plain-w480.webp');
    }

    public function test_optional_variant_failure_does_not_invalidate_primary_upload(): void
    {
        config(['livewire.temporary_file_upload.directory' => 'fixtures']);
        $this->source('fixtures/upload.webp', 600, 300);
        $file = new TemporaryUploadedFile('upload.webp', 'public');
        $disk = Storage::disk('public');
        $mock = \Mockery::mock($disk);
        $mock->shouldReceive('path')->andReturnUsing(function ($path) use ($disk) {
            if (str_contains($path, '-crop-w400')) {
                throw new \RuntimeException('Simulated derivative storage failure');
            }

            return $disk->path($path);
        });
        Storage::shouldReceive('disk')->with('public')->andReturn($mock);

        $path = FilamentWebpUpload::store($file, 'offers/hero-mobile', 1200, 900, 'failure', responsiveVariants: true);
        $this->assertSame('offers/hero-mobile/failure.webp', $path);
        $this->assertSame('image/webp', getimagesize($disk->path($path))['mime']);
    }

    public function test_srcset_only_includes_existing_consistent_variants_and_uses_storage_urls(): void
    {
        $primary = 'offers/hero-mobile/card.webp';
        $this->source($primary, 1200, 900);
        $disk = Storage::disk('public');
        $this->assertNull(FilamentWebpUpload::responsiveSrcset($primary));
        $this->assertNull(FilamentWebpUpload::responsiveSrcset('missing.webp'));
        $this->source('offers/hero-mobile/card-crop-w480.webp', 480, 360);
        $this->source('offers/hero-mobile/card-crop-w800.webp', 800, 400);
        $srcset = FilamentWebpUpload::responsiveSrcset($primary);
        $this->assertSame($disk->url('offers/hero-mobile/card-crop-w480.webp').' 480w, '.$disk->url($primary).' 1200w', $srcset);
        $this->assertCount(3, $disk->files('offers/hero-mobile'));
    }

    public function test_offer_card_markup_has_mobile_sizes_and_preserves_legacy_fallback(): void
    {
        $primary = 'offers/hero-mobile/card.webp';
        $this->source($primary, 1200, 900);
        FilamentWebpUpload::generateResponsiveVariants($primary);
        $item = new Offer(['title' => 'Local test', 'slug' => 'local-test', 'hero_mobile_image' => $primary, 'card_image' => 'offers/cards/desktop.webp']);
        $html = view('components.sections.item-list', ['model' => 'offer', 'items' => collect([$item]), 'attributes' => new ComponentAttributeBag])->render();
        $this->assertStringContainsString('sizes="calc(100vw - 26px)"', $html);
        $this->assertStringContainsString('card-crop-w480.webp 480w', $html);
        $this->assertStringContainsString('card-crop-w800.webp 800w', $html);
        $this->assertStringContainsString('card-crop-w1200.webp 1200w', $html);
        $this->assertStringContainsString('loading="lazy"', $html);
        $this->assertStringContainsString('aspect-[4/3]', $html);
        $this->assertStringContainsString('offers/cards/desktop.webp', $html);
        $item->hero_mobile_image = 'offers/hero-mobile/legacy.webp';
        $html = view('components.sections.item-list', ['model' => 'offer', 'items' => collect([$item]), 'attributes' => new ComponentAttributeBag])->render();
        $this->assertStringContainsString('srcset="'.asset('storage/offers/hero-mobile/legacy.webp').'"', $html);
        $this->assertStringNotContainsString('sizes=', $html);
    }
}
