<!DOCTYPE html>
<html lang="en-us" content="text/html" charset="UTF-8">
<head>
    <!-- <meta http-equiv="x-ua-compatible" content="ie=edge"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
          content="Enable businesses to be in touch with their customers anytime, anywhere with an integrated hosted-PBX SaaS solution.">
    <link rel="stylesheet" href="_css/main.css" type="text/css" media="screen, projection">
    <title>Nova VoIP - Official Home Page</title>
</head>
<body>
<header id="pageHeader">
    <nav id="mainNav" class="topnav">
        <ul id="mainNavlist">
            <li>
                <button id="hamBar" class="miniNav" onclick="miniNav()">&#9776;</button>
            </li>
            <li><a href="temp-home.blade.php" title="Nova VoIP" id="logo" class="mainMenu"><img
                        src="_img/Logo/Nova%20VoIP%20Logo.png" width=100%></a></li>
            <li class="subjects"><a href="#" class="mainMenu" id="learning">Learning</a></li>
            <li class="subjects"><a href="#" class="mainMenu" id="services">Services</a></li>
            <li class="subjects"><a href="#" class="mainMenu" id="packages">Packages</a></li>
            <li class="subjects"><a href="#" class="mainMenu" id="products">Products</a></li>
            <li class="subjects"><a href="#" class="mainMenu" id="support">Support</a></li>
            @guest
                <li><a href="{{ route('login') }}" class="userMenu" id="signIn">Sign in</a></li>
            @else
                <li><a href="{{ route('dashboard.panel') }}" class="userMenu" id="signIn">{{ Auth::user()->name }}</a></li>
            @endguest
            <li><a href="{{ route('hosted-pbx-session') }}" class="userMenu" id="cart">Cart</a></li>

            <li><input type="text" placeholder="Search.."></li>
        </ul>
    </nav>

</header>
<article>
    <section class="banner">
        <div class="slideshow-container">
            <a class="slideButtom">shop now ></a>
            <img class="mySlides1" src="_img/Main_Banner_1.png" style="width:100%">
            <img class="mySlides1" src="_img/Mid_Banner_1.png" style="width:100%">
            <button class="prev" onclick="plusDivs(-1, 0)">&#10094;</button>
            <button class="next" onclick="plusDivs(1, 0)">&#10095;</button>
        </div>


    </section>
    <h1>Benefits for Business</h1>
    <div id="tabSection">

        <div class="tabList">
            <ul class="tab">
                <li>
                    <button class="tablinks" id="firstBenefit" onmouseover="openBenefit(event, 'callFeatures')">Call
                        features
                    </button>
                </li>
                <li>
                    <button class="tablinks" id="secondBenefit" onmouseover="openBenefit(event, 'mobility')">Mobility
                    </button>
                </li>
                <li>
                    <button class="tablinks" id="thirdBenefit" onmouseover="openBenefit(event, 'conferencing')">
                        Conferencing
                    </button>
                </li>
                <li>
                    <button class="tablinks" id="forthBenefit" onmouseover="openBenefit(event, 'collaboration')">
                        Collaboration
                    </button>
                </li>
                <li>
                    <button class="tablinks" id="fifthBenefit" onmouseover="openBenefit(event, 'callanalytics')">Call
                        Analytics
                    </button>
                </li>
                <li>
                    <button class="tablinks" id="sixthBenefit" onmouseover="openBenefit(event, 'integration')">
                        Integration
                    </button>
                </li>
                <li>
                    <button class="tablinks" id="seventhBenefit" onmouseover="openBenefit(event, 'security')">Security
                    </button>
                </li>
                <li>
                    <button class="tablinks" id="eighthBenefit" onmouseover="openBenefit(event, 'QoS')">QoS</button>
                </li>
                <li>
                    <button class="tablinks" id="ninthBenefit" onmouseover="openBenefit(event, 'professionalServices')">
                        Professional Services
                    </button>
                </li>
            </ul>
        </div>
        <div class="tabContentSection">
            <div id="basicConcept" class="defaultTabContent">
                <h3>Experience the shift to Most Integrated Connected Communication.</h3>
                <p>Make and receive calls on your computer or mobile phone with VoIP. Nova VoIP's mobile app keeps you
                    connected when you're away from your office phone - or replaces your desk phone entirely.</p>
            </div>
            <div id="callFeatures" class="tabcontent">
                <h3>More than a dial tone.</h3>
                <ul>
                    <li><p>Unified communication with VoIP, video, &amp; chat</p></li>
                    <li><p>Bundle with helpdesk software &amp; Sales CRM</p></li>
                    <li><p>Unlimited calling, faxing, and texting nationwide</p></li>
                    <li><p>Amazing Service with 24/7 support</p></li>
                    <li><p>Free toll-free, virtual voicemail, &amp; auto attendant</p></li>
                </ul>
            </div>
            <div id="mobility" class="tabcontent">
                <h3>Work from anywhere.</h3>
                <ul>
                    <li><p>Top rated iOS &amp; Android mobile app</p></li>
                    <li><p>Easy to use desktop app for Windows and Mac</p></li>
                    <li><p>Send text messages from your mobile device</p></li>
                    <li><p>One-click conference calling from your cell phone</p></li>
                    <li><p>Declutter your workspace. Ditch the desk-phone</p></li>
                </ul>
            </div>
            <div id="conferencing" class="tabcontent">
                <h3>Conference calling.</h3>
                <ul>
                    <li><p>Host unlimited audio and video meetings</p></li>
                    <li><p>Audio conferencing with messaging &amp; screen share</p></li>
                    <li><p>Dedicated conference bridge for all your meetings</p></li>
                    <li><p>Crystal-clear, HD audio with no static on the line</p></li>
                    <li><p>Advanced VoIP conferencing with call recording</p></li>
                </ul>
            </div>
            <div id="collaboration" class="tabcontent">
                <h3>Bring teams together.</h3>
                <ul>
                    <li><p>Collaborate with your team, or individually</p></li>
                    <li><p>Create groups to connect via chat, video, &amp; audio</p></li>
                    <li><p>Chat, screen share, &amp; share files with each other</p></li>
                    <li><p>Schedule meetings, &amp; assign tasks with due dates</p></li>
                    <li><p>Keep track of conversations and team projects</p></li>
                </ul>
            </div>
            <div id="callanalytics" class="tabcontent">
                <h3>Reveal insights.</h3>
                <ul>
                    <li><p>Reveal the truth. Act with confidence.</p></li>
                    <li><p>Real-time data with custom reports &amp; dashboards</p></li>
                    <li><p>Identify business trends by analyzing call data</p></li>
                    <li><p>Call recording &amp; rating make QA a breeze</p></li>
                    <li><p>Gamify your employee performance data</p></li>
                </ul>
            </div>
            <div id="integration" class="tabcontent">
                <h3>Integrate your team's tools.</h3>
                <ul>
                    <li><p>Integrate your phone system with business apps</p></li>
                    <li><p>Boost efficiency by integrating your CRM and VoIP</p></li>
                    <li><p>Easy integration with Google, Microsoft, &amp; more</p></li>
                    <li><p>Click to dial phone numbers with business apps</p></li>
                    <li><p>Improve your business communication</p></li>
                </ul>
            </div>
            <div id="security" class="tabcontent">
                <h3>Peace of mind.</h3>
                <ul>
                    <li><p>Multi-layer security with 24/7 monitoring</p></li>
                    <li><p>TLS and SRTP encryption between all endpoints</p></li>
                    <li><p>Best-in-class session border controllers</p></li>
                    <li><p>SSAE 18 and ISO 27001 audited data centers</p></li>
                    <li><p>Innovative fraud detection &amp; mitigation</p></li>
                </ul>
            </div>
            <div id="QoS" class="tabcontent">
                <h3>The most reliable network.</h3>
                <ul>
                    <li><p>Highest rated call quality</p></li>
                    <li><p>All audio &amp; video calls in HD</p></li>
                    <li><p>Never miss a call, even with an Internet outage</p></li>
                    <li><p>Tailored solutions to match your network environment</p></li>
                    <li><p>Actively monitor your phone system’s call quality</p></li>
                </ul>
            </div>
            <div id="professionalServices" class="tabcontent">
                <h3>We obsess over you.</h3>
                <ul>
                    <li><p>We care for our team. But first, our customers</p></li>
                    <li><p>No scripts. No bots. 100% real people</p></li>
                    <li><p>Highest-rated unified communications company</p></li>
                    <li><p>Fully integrated services with core system</p></li>
                    <li><p>Most customer needed services all in one solution</p></li>
                </ul>
            </div>
        </div>
    </div>
    <section class="pakageSection">
        <h1>Select Best Package for Your Business</h1>
        <ul class="pakages">
            <li id="pakage1" class="pakage"><h2>Education</h2>
                <p>####</p><a href="#" title="Education">See Offer </a></li>
            <li id="pakage2" class="pakage"><h2>Financing</h2>
                <p>####</p><a href="#" title="Financing">See Offer </a></li>
            <li id="pakage3" class="pakage"><h2>Health Care</h2>
                <p>####</p><a href="#" title="Health Care">See Offer </a></li>
            <li id="pakage4" class="pakage"><h2>Other Business</h2>
                <p>####</p><a href="#" title="Other Business">See more </a></li>
        </ul>
    </section>
    <section class="banner">
        <div class="slideshow-container">
            <a class="slideButtom">Start now ></a>
            <img class="mySlides2" src="_img/Mid_Banner_1.png" style="width:100%">
            <img class="mySlides2" src="_img/Main_Banner_1.png" style="width:100%">
            <button class="prev" onclick="plusDivs(-1, 1)">&#10094;</button>
            <button class="next" onclick="plusDivs(1, 1)">&#10095;</button>
        </div>
    </section>
    <section class="productSecton">
        <h1>Products</h1>
        <ul class="products">
            <li id="product1" class="product"><h2>IP Phones</h2>
                <p>Find most qualified IP Phones work with our network.</p><a href="">Shop now</a></li>
            <li id="product1" class="product"><h2>VoIP Modules</h2>
                <p>All modules that you need to have full operated VoIP system.</p><a href="">Shop now</a></li>
            <li id="product1" class="product"><h2>Network Devices</h2>
                <p>If you need network devicea to run your network, you can find here.</p><a href="">Shop now</a></li>
            <li id="product1" class="product"><h2>VoIP Software</h2>
                <p>Need a customized VoIP software, check here.</p><a href="">Shop now</a></li>
        </ul>
    </section>
    <section class="banner">
        <div class="slideshow-container">
            <img src="_img/Low_Banner_1.png">
            <a class="slideButtom">Learn more ></a>
        </div>
    </section>
</article>
<footer id="pageFooter">
    <section>
        <span>Follow Nova VoIP </span>
        <ul class="socialMediaLinks">
            <li id="Facebook"></li>
            <li id="twitter"></li>
            <li id="linkedin"></li>
            <li id="instagram"></li>
        </ul>
    </section>
    <section id=extraNav>
        <ul>
            <li id="language">
                <div class="customSelect">
                    <select id="lang-switch">
                        <option value="0"> English</option>
                        <option value="1"> English</option>
                        <option value="2"> French</option>

                    </select>
                </div>
            </li>
            <li><a href="#" class="extraNavBar" id="siteMap">Sitemap</a></li>
            <li><a href="#" class="extraNavBar" id="privacyAndCookies">Privacy &amp; Cookies</a></li>
            <li><a href="#" class="extraNavBar" id="termsOfUse">Terms of use</a></li>
            <li><a href="#" class="extraNavBar" id="trademarks">Trademarks</a></li>
            <li><a href="#" class="extraNavBar" id="safety">Safety</a></li>
            <li><a href="#" class="extraNavBar" id="aboutUs">About us</a></li>
            <li id="copyRight"> &copy;Nova VoIP</li>
        </ul>
    </section>
</footer>
<script src="_java/Script_main.js" type="text/javascript" charset="UTF-8"></script>
</body>
</html>
