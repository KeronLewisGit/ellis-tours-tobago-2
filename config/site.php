<?php

/*
|--------------------------------------------------------------------------
| Ellis Tours Tobago — site content
|--------------------------------------------------------------------------
| Everything Ellis is likely to change that is not a tour (contact details,
| tour types, questions, form options) lives here so the views and the
| forms stay in sync. Tours, prices and reviews are edited in the admin.
| Wording is drawn from ellistourstobago.com and the company's Instagram.
*/

return [

    'name' => 'Ellis Tours Tobago',
    'short' => 'Ellis Tours',
    'descriptor' => 'Guided tours of Tobago',
    'tagline' => 'Explore the greatest little island.',
    'intro' => 'Rainforest walks, island tours and boat trips with Ellis Clarke, a licensed Tobago tour guide with more than twenty years of experience. Book and pay online, or pay in cash at pickup.',
    'years' => 20,

    'guide' => [
        'name' => 'Ellis Clarke',
        'role' => 'Licensed tour guide',
        'bio' => 'I am a licensed tour guide with over twenty years of experience. Book now to explore the greatest little island.',
        'image' => 'images/photos/img_4068.webp',
    ],

    'phone' => '(868) 302-4379',
    'phone_href' => '+18683024379',
    'whatsapp' => '18683024379',
    'email' => 'ellisclarketourstt@gmail.com',

    'location' => 'Crown Point, Tobago',
    'address' => 'Pigeon Point Beach Facility, Crown Point, Tobago',
    'timezone' => 'America/Port_of_Spain',
    'currency' => 'US$',

    'links' => [
        'instagram' => 'https://www.instagram.com/ellistourstobago/',
        'facebook' => 'https://www.facebook.com/ellistourstobago',
        'tiktok' => 'https://www.tiktok.com/@ellistourstobago',
        'tripadvisor' => 'https://www.tripadvisor.com/Attraction_Review-g1026381-d19727531-Reviews-Ellis_Clarke-Crown_Point_Tobago_Trinidad_and_Tobago.html',
        'maps' => 'https://www.google.com/maps/search/?api=1&query=Pigeon+Point+Beach+Tobago',
    ],

    // Source: Instagram profile, October 2026.
    'social' => [
        'instagram_handle' => '@ellistourstobago',
        'instagram_posts' => 175,
    ],

    // The three kinds of tour, in the order they appear on the site.
    'categories' => [
        'walks' => [
            'name' => 'Rainforest Walks',
            'icon' => 'trees',
            'eyebrow' => 'Guided walks and hikes',
            'summary' => 'Low-intensity guided walks to the waterfalls of the Main Ridge rainforest.',
            'intro' => 'Enjoy sightseeing along the Caribbean coastline, with stops for quick photos, on your way to a guided low-intensity walk through the Tobago Main Ridge Forest Reserve, the world’s oldest protected rainforest reserve, recognised by UNESCO. It is an engaging and educational tour of the local flora, fauna and tropical forest ecology, with a few species found nowhere else.',
            'image' => 'images/photos/img_2681.webp',
            'topic' => 'Rainforest walks',
        ],
        'island' => [
            'name' => 'Island Tours',
            'icon' => 'map',
            'eyebrow' => 'Guided land tours',
            'summary' => 'Hidden gems and iconic spots along the Caribbean and Atlantic coasts.',
            'intro' => 'Prepare for a day like no other as we guide you through the hidden gems and iconic spots of our beautiful island. From stunning vistas to local legends, each stop reveals a piece of what makes Tobago so extraordinary.',
            'image' => 'images/photos/img_3133.webp',
            'topic' => 'Island tours',
        ],
        'boat' => [
            'name' => 'Boat Tours',
            'icon' => 'sailboat',
            'eyebrow' => 'On the water',
            'summary' => 'Reef snorkelling, the Nylon Pool and beaches you can only reach by boat.',
            'intro' => 'Join us for a journey on the water, where stunning landscapes, rich history and unique wildlife come to life. Our boat tours are made for explorers of all ages: a relaxed yet thrilling escape with experienced guides.',
            'image' => 'images/photos/img_2445.webp',
            'topic' => 'Boat tours',
        ],
    ],

    // What guests see along the way. Named places come from the tour pages and the company's own Instagram captions.
    'highlights' => [
        ['trees', 'Main Ridge rainforest', 'Walk the Gilpin Trace trail through the world’s oldest protected rainforest reserve.', 'images/photos/img_4968.webp'],
        ['droplets', 'Waterfalls', 'Five waterfalls on the long rainforest walk, with a pool to cool down in.', 'images/photos/img_3846.webp'],
        ['bird', 'Hummingbirds', 'Feed hummingbirds by hand over lunch at Hummingbird Heaven.', 'images/photos/img_2549.webp'],
        ['fish', 'Buccoo Reef', 'Snorkel the reef, then float in the shallow Nylon Pool.', 'images/photos/img_2446.webp'],
        ['anchor', 'Pirate’s Bay', 'Take a boat from Charlotteville to Pirate’s Bay and Pink Sand Bay.', 'images/photos/img_2130-1.webp'],
        ['camera', 'Coastal lookouts', 'Photo stops along both the Caribbean and the Atlantic coast.', 'images/photos/img_4649.webp'],
    ],

    'process' => [
        ['compass', 'Choose your tour', 'Pick a tour and check your date on the calendar.'],
        ['ticket', 'Book online or send a request', 'Reserve and pay online, or send a request and pay in cash at pickup.'],
        ['message-circle', 'Ellis confirms', 'You get a WhatsApp message or email confirming your pickup time and place.'],
        ['bus', 'Hotel pickup', 'Most tours start with a 9am pickup. Private tours can leave when it suits you.'],
    ],

    'promises' => [
        ['badge-check', 'Licensed guide', 'Ellis Clarke is a licensed tour guide with more than twenty years in Tobago’s forests and bays.'],
        ['bus', 'Hotel pickup and drop-off', 'Every tour includes pickup from where you are staying, and a ride back.'],
        ['wallet', 'Pay online or in cash', 'Reserve now and pay online, or pay in cash at pickup.'],
        ['users', 'Group or private', 'Join a shared group tour, or keep the day to your own party.'],
    ],

    'faqs' => [
        ['How do I pay?', 'You can reserve your tour and pay online when you book, or send a booking request and pay in cash at pickup. Prices are in US dollars, per person.'],
        ['What is the difference between a group and a private tour?', 'Group tours may include other groups, families or individuals. A private tour is for your party only, and the hotel pickup time is flexible.'],
        ['Where do you pick up from?', 'Every tour includes hotel pickup and drop-off. Tell us where you are staying when you book. Most tours leave at 9am, and the Buccoo Reef tour at 10am.'],
        ['How fit do I need to be for the rainforest walks?', 'Both walks are guided and low intensity. The long walk is about 5 km with rest stops and snacks along the way. The short walk is about 1.5 km there and back.'],
        ['What should I wear and bring?', 'For the rainforest: comfortable long clothing, walking shoes or water shoes with good grip, sunscreen, a hat, mosquito repellent and a water bottle. For boat and island tours: swimwear, sunscreen, a towel and a waterproof pouch for your phone. Each tour page has its own list.'],
        ['Is lunch included?', 'A local lunch is included on the 3-hour rainforest walk, the full-day island tour and the Pirate’s Bay tour. The half-day tours and the Buccoo Reef tour do not include lunch.'],
        ['What if it rains?', 'It can rain in the forest at any time of year, so pack a change of clothes. If you are unsure about the weather on the day, message Ellis on WhatsApp before pickup.'],
        ['I am arriving by cruise ship. Can I still do a tour?', 'Yes. Send your docking and all-aboard times with the group form and we will suggest a tour that gets you back in good time.'],
        ['Do you take school groups and large families?', 'Yes. Use the group form to tell us your numbers and what you would like to see, and we will put a day together for you.'],
    ],

    // Options for the booking and group forms.
    'payment_methods' => ['Cash at pickup', 'Pay online'],
    'contact_methods' => ['WhatsApp', 'Phone call', 'Email'],
    'heard' => ['Instagram', 'Facebook', 'TikTok', 'Tripadvisor', 'Google', 'My hotel or host', 'A friend or family member', 'Other'],
    'group_types' => ['School or youth group', 'Cruise ship visitors', 'Family or friends', 'Company or team', 'Other'],
    'interests' => ['Rainforest and waterfalls', 'Hummingbirds and wildlife', 'Island sightseeing', 'Buccoo Reef and Nylon Pool', 'Pirate’s Bay and beaches', 'Local food'],
    'topics' => ['General enquiry', 'Rainforest walks', 'Island tours', 'Boat tours', 'Group or school tour', 'Cruise ship visit', 'An existing booking'],

    // Recent posts shown on the home page: [image in public/images/feed, Instagram shortcode, description].
    'feed' => [
        ['feed-1.jpg', 'DdotyAgRAnJ', 'A hummingbird feeding from a hand-held feeder'],
        ['feed-2.jpg', 'DeIdyvYka3o', 'Cooling down at a waterfall deep in the rainforest'],
        ['feed-3.jpg', 'DdxWSNnR3En', 'Walking down the jetty to the reef boats'],
        ['feed-4.jpg', 'CsHpK7mRCs2', 'Snorkelling in clear turquoise water'],
        ['feed-5.jpg', 'Cne0M7Sp_eu', 'The golden waterfall in the rainforest'],
        ['feed-6.jpg', 'CuA-yAMREir', 'The view over Speyside'],
        ['feed-7.jpg', 'CsFWM1aOWMD', 'Guests standing in the Nylon Pool beside the boat'],
        ['feed-8.jpg', 'CmiLSiTOoXY', 'A turtle hatchling crossing the sand'],
    ],

    // Addresses on the old WordPress site, so links and search results keep working.
    'redirects' => [
        'tours-2' => '/tours',
        'bookings' => '/book',
        'guided-walking-hike-tours' => '/tours#walks',
        'guided-land-tours-excursions' => '/tours#island',
        'boat-tours-excursions' => '/tours#boat',
        '3hr-rainforest-walk' => '/tours/3hr-rainforest-walk',
        '1-5hr-rainforest-walk' => '/tours/1-5hr-rainforest-walk',
        'full-day-island-tour' => '/tours/full-day-island-tour',
        'half-day-island-tour' => '/tours/half-day-island-tour',
        'tour-to-pirate-bay-full-day' => '/tours/pirates-bay-full-day',
        '4x4-off-road-tour-buccoo-reef-nylon-pool' => '/tours/buccoo-reef-nylon-pool',
        '4x4-off-road-tour' => '/tours/4x4-off-road-tour',
        '3hr-rainforest-walk-group' => '/book?tour=3hr-rainforest-walk&rate=group',
        '3hr-rainforest-tour-private' => '/book?tour=3hr-rainforest-walk&rate=private',
        'book-1-5-hr-rainforest-walk-group' => '/book?tour=1-5hr-rainforest-walk&rate=group',
        'book-1-5-hr-rainforest-walk-private' => '/book?tour=1-5hr-rainforest-walk&rate=private',
        'book-full-day-island-tour-group' => '/book?tour=full-day-island-tour&rate=group',
        'full-day-island-tour-private' => '/book?tour=full-day-island-tour&rate=private',
        'book-half-day-island-tour' => '/book?tour=half-day-island-tour&rate=group',
        'book-half-day-island-tour-private' => '/book?tour=half-day-island-tour&rate=private',
        'book-full-day-tour-to-pirate-bay-group' => '/book?tour=pirates-bay-full-day&rate=group',
        'book-full-day-tour-to-pirate-bay-private' => '/book?tour=pirates-bay-full-day&rate=private',
        'book-buccoo-reef-nylon-pool-experience' => '/book?tour=buccoo-reef-nylon-pool',
    ],

];
