-- Update the existing FAQ CMS records only; preserve hero, H1 and URL.
SET NAMES utf8mb4;
START TRANSACTION;
UPDATE pages SET
 meta_title = 'Frequently Asked Questions | Nandini Jungle Ubud',
 meta_description = 'Find answers about Nandini Jungle by Hanging Gardens in Bali, including resort location, transportation, accommodation, dining, spa, activities and reservations.',
 description = '<p>Practical information about your visit, from transport and accommodation to dining, wellness and reservations.</p>',
 updated_at = NOW()
WHERE id = 25 AND site = 'main' AND page_name = 'FAQ Page';
UPDATE page_sections SET description = '<h2>Location &amp; Transportation</h2>
<h3>Where is Nandini Jungle by Hanging Gardens located?</h3><p>Nandini Jungle by Hanging Gardens is located in Banjar Susut, Desa Buahan, Payangan, Bali 80571, Indonesia, within the greater Ubud area. The resort overlooks the Ayung River valley and is outside Ubud town centre.</p>
<h3>How far is the resort from central Ubud?</h3><p>The journey to central Ubud usually takes around 30–40 minutes by car, depending on traffic. Reception can assist with transport arrangements.</p>
<h3>How far is the resort from Ngurah Rai International Airport?</h3><p>The journey from Ngurah Rai International Airport (DPS) typically takes around 90 minutes by car, depending on traffic. Allow additional time when planning your flight departure.</p>
<h3>Does the resort arrange airport transfers?</h3><p>Yes, airport transfers can be arranged through the resort. <a href="/contact-us">Contact our team</a> before arrival to confirm arrangements and pricing.</p>
<h3>Is there a shuttle service to Ubud?</h3><p>The resort offers a scheduled shuttle to Ubud town centre. Ask reception for the current timetable and check whether the service is included in your booking.</p>
<h3>Is the resort suitable for guests with limited mobility?</h3><p>The resort occupies a steep hillside, with stairs and slopes in some areas. Discuss your mobility needs with the team before booking so they can advise on accommodation and access.</p>
<h2>Accommodation &amp; Reservations</h2>
<h3>What are the check-in and check-out times?</h3><p>Check-in is from 14:00 and check-out is by 12:00, as listed in the official booking engine.</p>
<h3>What accommodation categories are available?</h3><p>Nandini offers Jungle View, Sunrise View and Panoramic Jungle View Villas, as well as Royal Suites. Explore the <a href="/jungle-villas">Jungle Villas</a> and <a href="/the-royal-suites">Royal Suites</a> for individual room details.</p>
<h3>Do all villas have private swimming pools?</h3><p>No, the Jungle View, Sunrise View and Panoramic Jungle View Villas are not private-pool villas. Their outdoor bathtubs and rain showers are separate bathroom amenities, not swimming pools.</p>
<h3>Are children welcome?</h3><p>Yes, children are welcome at Nandini. Confirm the selected room’s occupancy and any child charges with the reservations team before booking.</p>
<h3>How can guests make a reservation?</h3><p>Guests can reserve through the <a href="https://nandinijunglebyhanginggardens.reserve-online.net/">official booking engine</a> or contact the reservations team. Select your dates, accommodation and rate to review availability and the applicable terms.</p>
<h3>What are the cancellation and modification policies?</h3><p>The terms attached to your selected rate or package govern cancellations and modifications. Some published offers are non-refundable; review the terms before payment and refer to your confirmation for an existing reservation.</p>
<h2>Dining &amp; Spa</h2>
<h3>Are the restaurants open to outside guests?</h3><p>Yes, outside guests are welcome to dine at Nandini. Advance reservation is recommended; visit the <a href="https://dining.nandinibali.com/">dining website</a> to plan your visit.</p>
<h3>What dining options are available?</h3><p>Dining options include Wild Ginger Restaurant, Alang Bar &amp; Lounge, Afternoon Tea and romantic or private dining experiences. Luxe High Tea is a separate experience from Afternoon Tea; confirm the menu and inclusions for your chosen experience.</p>
<h3>Is advance dining reservation recommended?</h3><p>Yes, advance reservations are recommended, especially for dinner, romantic dining and special occasions. Walk-in availability may vary.</p>
<h3>Do spa treatments require advance reservation?</h3><p>Advance booking is recommended for spa treatments. Explore the <a href="https://spa.nandinibali.com/">Essence Spa website</a> and contact the spa team to confirm your treatment and preferred time.</p>
<h3>What wellness experiences are available?</h3><p>Wellness experiences include Balinese spa treatments, Signature Spa on the River, wine-inspired spa rituals, yoga, meditation and selected riverside experiences. Confirm availability and scheduling for your chosen experience before your visit.</p>
<h2>Activities &amp; Special Occasions</h2>
<h3>What activities are available at Nandini?</h3><p>Nandini offers cooking classes, village and cycling experiences, yoga, riverside rituals and romantic dining. Browse the <a href="/experiences">experiences page</a> for current options, including excursions outside the resort.</p>
<h3>Should activities be booked in advance?</h3><p>Arrange your preferred activities with the team before your visit to confirm availability, timing and any special arrangements. Check the details of each experience rather than assuming it is included in your stay.</p>
<h3>Does the resort host weddings or intimate ceremonies?</h3><p>Yes, Nandini hosts weddings and intimate ceremonies. Visit the <a href="/weddings">weddings page</a> and contact the team to discuss dates, guest numbers and arrangements.</p>
<h3>Does Nandini offer honeymoon experiences?</h3><p>Yes, Nandini offers honeymoon packages and experiences for couples, including selected spa and romantic dining arrangements. Explore the <a href="/honeymoon">honeymoon page</a> for current options and inclusions.</p>', updated_at = NOW()
WHERE page_id = 25 AND section_key = 'intro_text_section' AND id = 61;
COMMIT;

