<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us — Tiffany Replica</title>
    <link rel="shortcut icon" href="{{ asset('images/smallIcon.jpg') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800">

    {{-- Site Navbar --}}
    @include('components.navbar')

    <main>
        <section class="max-w-7xl mx-auto px-6 md:px-8 lg:px-10 py-16 md:py-24">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                <!-- Text column -->
                <div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif tracking-tight text-gray-900">
                        Meet The Tiffany Replica
                    </h1>
                    <p class="mt-4 text-xl text-gray-500">
                        Crafted for Timeless Elegance
                    </p>
                    <p class="mt-8 leading-7 text-gray-700">
                        Tiffany Replica was born from a love of classic beauty and the desire to make timeless jewelry
                        accessible to everyone. Our store specializes in recreating the elegance of Tiffany-inspired
                        designs with precision, offering pieces that embody luxury without the high price tag. Every ring,
                        necklace, and bracelet is crafted with attention to detail, blending sophistication with everyday
                        wearability. Whether you are celebrating a milestone, expressing love, or simply elevating your
                        personal style, Tiffany Replica jewelry is created to shine with you in every moment. More than
                        just accessories, our pieces are symbols of confidence, elegance, and affordable luxury.
                    </p>
                </div>

                <!-- Image column -->
                <div class="relative w-full h-64 sm:h-80 md:h-[28rem] lg:h-[32rem] overflow-hidden rounded md:rounded-none md:rounded-r-lg">
                    <img
                        src="{{ asset('images/aboutus1.webp') }}"
                        alt="About Tiffany Replica"
                        class="absolute inset-0 w-full h-full object-cover">
                </div>
            </div>
        </section>

        <!-- Stats / Highlights section -->
        <section class="bg-gray-50 py-16 md:py-20">
            <div class="max-w-7xl mx-auto px-6 md:px-8 lg:px-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <!-- Card 1 -->

                    <div>
                        <div class="text-5xl md:text-6xl font-semibold text-[#c9b57b]">500+</div>
                        <h3 class="mt-3 text-xl md:text-2xl font-semibold text-gray-900">Timeless Designs</h3>
                        <p class="mt-4 text-gray-600 leading-7">
                            Our collection features over 500 carefully crafted designs inspired by Tiffany’s iconic elegance.
                            From sparkling engagement rings to everyday classics, each piece captures the beauty of timeless
                            jewelry, making luxury accessible to everyone.
                        </p>
                    </div>

                    <!-- Card 2 -->
                    <div class="md:border-l md:border-gray-200 md:pl-10">
                        <div class="text-5xl md:text-6xl font-semibold text-[#c9b57b]">1000+</div>
                        <h3 class="mt-3 text-xl md:text-2xl font-semibold text-gray-900">Happy Customers</h3>
                        <p class="mt-4 text-gray-600 leading-7">
                            We’ve helped more than 1,000 customers celebrate love, milestones, and personal style with jewelry
                            that looks and feels luxurious—without the luxury price tag. Every piece comes with our promise of
                            quality and customer satisfaction.
                        </p>
                    </div>

                    <!-- Card 3 -->
                    <div class="md:border-l md:border-gray-200 md:pl-10">
                        <div class="text-5xl md:text-6xl font-semibold text-[#c9b57b]">70%</div>
                        <h3 class="mt-3 text-xl md:text-2xl font-semibold text-gray-900">Savings on Luxury</h3>
                        <p class="mt-4 text-gray-600 leading-7">
                            Why pay more for elegance? With Tiffany Replica, you can enjoy the same sophisticated look and
                            feel as high-end jewelry at a fraction of the cost—saving up to 70% compared to traditional luxury
                            brands, without compromising on beauty.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Optional footer spacing --}}
    <div class="h-10"></div>
    
       <footer class="footer"><!--Footer-->
        <div class="container-footer">
            <div class="row"><!--Arrange All DIV IN ROW-->
                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="{{ route('aboutus') }}">About Us</a></li>
                        <li><a href="{{ route('home') }}">Privacy Policy</a></li>

                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Get Help</h4>
                    <ul>
                        <li><a href="{{ route('faq.index') }}">FAQ</a></li>


                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Shop</h4>
                    <ul>
                        <li><a href="{{ route('products.index') }}">Shop All Jewelry</a></li>
                        <li><a href="{{ route('wishlist.index') }}">Wishlist</a></li>

                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Follow Us</h4>
                    <div class="social-links"><!--Social Icon-->
                        <a href="https://www.facebook.com/nike/">
                            <svg style=" width: 16px;height:16px" aria-hidden="true" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z" />
                            </svg></a>


                        <a href="https://twitter.com/adidas"> <svg style=" width: 16px;height:16px"
                                aria-hidden="true" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 512 512">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z" />
                            </svg></a>
                        <a href="https://www.instagram.com/puma/"><svg style=" width: 16px;height:16px"
                                aria-hidden="true" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 448 512">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z" />
                            </svg></a>




                        <a href="https://www.linkedin.com/company/under-armour"> <svg style=" width: 16px;height:16px"
                                aria-hidden="true" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 448 512">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M100.3 448H7.4V148.9h92.9zM53.8 108.1C24.1 108.1 0 83.5 0 53.8a53.8 53.8 0 0 1 107.6 0c0 29.7-24.1 54.3-53.8 54.3zM447.9 448h-92.7V302.4c0-34.7-.7-79.2-48.3-79.2-48.3 0-55.7 37.7-55.7 76.7V448h-92.8V148.9h89.1v40.8h1.3c12.4-23.5 42.7-48.3 87.9-48.3 94 0 111.3 61.9 111.3 142.3V448z" />
                            </svg>
                        </a>

                    </div>
                </div>
            </div>
        </div>
        <div class="Copyright"><!--Copy right-->
            <p>Copyright &COPY; 2025 TIFFANY REPLICA All right reserved.</p>
        </div>
    </footer>
</body>
</html>
