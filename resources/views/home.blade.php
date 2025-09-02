<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    @vite(['resources/css/app.css', 'resources/css/style.css'])
    <link rel="shortcut icon" href="assests/icon.png" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])


</head>

<body>
<header id="mainNav"
  class="fixed top-0 left-0 w-full z-50 transition-colors duration-300 ease-in-out bg-transparents">

    <div class="max-w-7xl mx-auto flex flex-col items-center">
        {{-- Logo --}}
        <div class="py-4">
            <a href="{{ url('/') }}" class="text-3xl font-serif tracking-widest">TIFFANY REPLICA</a>
        </div>

        {{-- Main Navigation --}}
        <ul id="navLinks"
            class="relative flex space-x-10 text-sm font-medium text-white transition-colors duration-300">

            {{-- LOVE & ENGAGEMENT --}}
            <li class="group relative">
                <a href="#"
                   class="inline-block px-2 pb-1 relative transition
                          after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
                          after:transition-all after:duration-300 group-hover:after:w-full">
                   LOVE & ENGAGEMENT
                </a>

                {{-- Full-width dropdown (fixed to viewport) --}}
                <div
                    class="fixed left-0 top-[86px] hidden w-screen bg-white shadow-xl border-t border-gray-200
                           group-hover:block animate-fadeSlide z-40">
                    
                    {{-- Inner container keeps content centered --}}
                    <div class="max-w-7xl mx-auto px-8 py-10">
                        <div class="grid grid-cols-4 gap-8">

                            {{-- Column 1 --}}
                            <div>
                                <h3 class="text-gray-900 font-semibold mb-3">Categories</h3>
                                <ul class="space-y-2 text-gray-600">
                                    <li><a href="#">Engagement Rings</a></li>
                                    <li><a href="#">Wedding Bands</a></li>
                                    <li><a href="#">Couple’s Rings</a></li>
                                    <li><a href="#">Women’s Wedding Bands</a></li>
                                    <li><a href="#">Men’s Wedding Bands</a></li>
                                </ul>
                                <h3 class="text-gray-400 font-semibold mt-6 mb-2">Shop By Shape</h3>
                                <ul class="space-y-2 text-gray-600">
                                    <li><a href="#">Round</a></li>
                                    <li><a href="#">Oval</a></li>
                                    <li><a href="#">Emerald</a></li>
                                    <li><a href="#">Cushion</a></li>
                                </ul>
                            </div>

                            {{-- Column 2 --}}
                            <div>
                                <h3 class="text-gray-900 font-semibold mb-3">Collections</h3>
                                <ul class="space-y-2 text-gray-600">
                                    <li><a href="#">The Tiffany® Setting</a></li>
                                    <li><a href="#">Tiffany True®</a></li>
                                    <li><a href="#">Tiffany Harmony®</a></li>
                                    <li><a href="#">Tiffany Soleste®</a></li>
                                    <li><a href="#">Tiffany Novo®</a></li>
                                    <li><a href="#">Jean Schlumberger</a></li>
                                    <li><a href="#">Tiffany Together</a></li>
                                    <li><a href="#">Tiffany Forever</a></li>
                                    <li><a href="#">T&CO.™</a></li>
                                </ul>
                            </div>

                            {{-- Column 3 --}}
                            <div>
                                <h3 class="text-gray-900 font-semibold mb-3">The Tiffany Difference</h3>
                                <ul class="space-y-2 text-gray-600">
                                    <li><a href="#">A Tiffany Ring</a></li>
                                    <li><a href="#">Tiffany Lifetime Service</a></li>
                                    <li><a href="#">Responsible Sourcing</a></li>
                                    <li><a href="#">How to Choose an Engagement Ring</a></li>
                                    <li><a href="#">How to Choose a Wedding Band</a></li>
                                </ul>
                            </div>

                            {{-- Column 4 (images) --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <img src="/images/ring1.jpg" class="w-full rounded" alt="ring">
                                    <p class="text-sm mt-2">A Tiffany Ring</p>
                                </div>
                                <div>
                                    <img src="/images/ring2.jpg" class="w-full rounded" alt="craft">
                                    <p class="text-sm mt-2">Craftsmanship</p>
                                </div>
                                <div>
                                    <img src="/images/ring3.jpg" class="w-full rounded" alt="diamonds">
                                    <p class="text-sm mt-2">The Guide to Diamonds</p>
                                </div>
                                <div>
                                    <img src="/images/ring4.jpg" class="w-full rounded" alt="appointment">
                                    <p class="text-sm mt-2">Book An Appointment</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </li>

            {{-- Other nav items --}}
            <li class="group relative">
                <a href="#"
                   class="inline-block px-2 pb-1 relative transition
                          after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
                          after:transition-all after:duration-300 group-hover:after:w-full">
                   HIGH JEWELRY
                </a>
            </li>
            <li class="group relative">
                <a href="#"
                   class="inline-block px-2 pb-1 relative transition
                          after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
                          after:transition-all after:duration-300 group-hover:after:w-full">
                   FINE WATCHES
                </a>
            </li>
            <li class="group relative">
                <a href="#"
                   class="inline-block px-2 pb-1 relative transition
                          after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
                          after:transition-all after:duration-300 group-hover:after:w-full">
                   GIFTS
                </a>
            </li>
        </ul>
    </div>
</header>

    <div class="homepage-container-1" id="homepage">
        <div class="homepage-container-1-1">
            <br>
            <h1>Enchanting time with the métiers d'art</h1>
            <p>Craftsmen breathe life into a story.
            </p>
            <br><br>
        </div>
        <video src="{{ asset('videos/hero-video.mp4') }}" autoplay muted loop></video>
    </div>
    <main>
        <div class="homepage-container-2">
            <div class="homepage-container-2-2">
                <h1>Own Real Estate, One Token at a Time<h1>

                        <p>Transform the way you invest in real estate with our tokenized platform. By dividing
                            properties into digital tokens, we make it easy for everyone to access lucrative real estate
                            opportunities. Invest in properties worldwide, enjoy rental income, and trade tokens
                            effortlessly on a secure blockchain-backed marketplace. Join us in democratizing property
                            ownership!.</p>
            </div>
            <div class="homepage-container-2-1"><img src="assests/image-5.webp" alt=""></div>

        </div>

        <div class="homepage-container-3">
            <div class="homepage-container-3-2">
                <h1>Invest Smarter, Invest Together</h1>

                <p>Say goodbye to traditional real estate barriers. With tokenization, you can now co-own premium
                    properties by purchasing affordable tokens. Our platform provides transparency, flexibility, and
                    accessibility for investors and property owners alike. Discover how real estate tokenization is
                    reshaping the industry by empowering individuals to create wealth with minimal risk. </p>
            </div>
            <div class="homepage-container-3-1"><img src="assests/image-6.jpg" alt=""></div>

        </div>

        <div class="homepage-container-4">
            <div class="homepage-container-4-1" style="text-align: right; width: 700px; padding-top: 50px;">
                <h1>Creating modern and <br> comfortable spaces for living</h1>
            </div>
            <div class="homepage-container-4-2">
                <div class="homepage-container-4-2-1"><img src="assests/image-3.webp" alt="" width="150px">
                </div>
                <div class="homepage-container-4-2-2"><img src="assests/image-1.jpg" alt="" width="450px"
                        height="500px"></div>

                <div class="homepage-container-4-2-3">
                    <h1 style="font-weight: bold;">Effortless Renting, Redefined for Modern Living"</h1>
                    <br>
                    <p style="font-weight: bolder;">Discover a smarter way to rent with our innovative platform
                        designed
                        to simplify the process for both tenants and property owners. Whether you're searching for the
                        perfect home or looking to lease your property, we bring ease and efficiency to every step. Our
                        system ensures transparency, secure transactions, and flexible options tailored to your needs.
                        For tenants, explore a wide range of modern, comfortable spaces to suit your lifestyle. For
                        property owners, streamline the rental process with tools that help you manage listings, track
                        payments, and connect with potential tenants effortlessly. Embrace a hassle-free renting
                        experience that prioritizes convenience and satisfaction for all.</p>
                </div>
                <div class="homepage-container-4-2-4"><img src="assests/image-4.webp" alt="" width="200px"
                        height="200px"></div>

            </div>
        </div>

        <div class="homepage-container-5">
            <div class="homepage-container-5-1">
                <div class="content">
                    Our Story </div>
            </div>
            <div class="homepage-container-5-2">
                <h1>
                    "Building Dreams, One Space at a Time"
                </h1>
                <p>"Our story began with a simple mission: to make real estate accessible, transparent, and inclusive
                    for everyone. We believe that property ownership and renting should be more than just
                    transactions—it should be a pathway to creating better lives and stronger communities. With this
                    vision, we set out to revolutionize the real estate industry by combining modern technology,
                    innovative solutions, and a deep understanding of what people need in their living and investment
                    spaces." </p>

                <br>
                <p>
                    "Over the years, we have grown into a platform that empowers individuals to take control of their
                    real estate journeys, whether through investing, renting, or managing properties. By leveraging the
                    power of blockchain and tokenization, we’ve opened doors to opportunities that were once out of
                    reach. But our story doesn’t end here—we’re continuously evolving, driven by the passion to create
                    spaces that are not only modern and comfortable but also sustainable and future-ready. Join us as we
                    continue to build dreams and redefine the way people connect with real estate."
                </p>
            </div>
        </div>




    </main>

    <footer class="footer"><!--Footer-->
        <div class="container-footer">
            <div class="row"><!--Arrange All DIV IN ROW-->
                <div class="footer-col">
                    <h4>company</h4>
                    <ul>
                        <li><a href="../About-us/aboutus.html">about us</a></li>
                        <li><a href="../Services/termNcondition.html">privacy policy</a></li>

                    </ul>
                </div>
                <div class="footer-col">
                    <h4>get help</h4>
                    <ul>
                        <li><a href="../Services/service.html">FAQ</a></li>


                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="../Product-Page/product.html?filter=all">Renting</a></li>
                        <li><a href="../Product-Page/product.html?filter=all">Investing</a></li>

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
                            </svg></i></a>
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
            <p>Copyright &COPY; 2024 Next.Chain All right reserved.</p>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/ethers@5.7.2/dist/ethers.umd.min.js"></script>
    <script src="app1.js"></script>

    <script>
document.addEventListener("DOMContentLoaded", () => {
  const nav = document.getElementById("mainNav");
  const links = document.getElementById("navLinks");

  function handleScroll() {
    const scrolled = window.scrollY > 50; // trigger earlier for reliability
    if (scrolled) {
      nav?.classList.add("bg-white", "shadow-md");
      nav?.classList.remove("bg-transparent");
      links?.classList.add("text-gray-800");
      links?.classList.remove("text-white");
    } else {
      nav?.classList.add("bg-transparent");
      nav?.classList.remove("bg-white", "shadow-md");
      links?.classList.add("text-white");
      links?.classList.remove("text-gray-800");
    }
  }

  window.addEventListener("scroll", handleScroll, { passive: true });
  handleScroll(); // run once on load
});
    </script>
</body>

</html>
