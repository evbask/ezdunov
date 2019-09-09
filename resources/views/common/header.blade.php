<!-- Header Starts -->
<header class="header" style="background-color: black; padding-bottom: 0px;position: sticky" >
            <div class="header-inner">
                <!-- navbar Starts -->
				<nav class="navbar">
					<div id="menu_swither"><a id="toggle_nav_btn" class="toggle-left-nav-btn inline-block ml-20 pull-left" href="javascript:void(0);"><i class="zmdi zmdi-menu"></i></a></div>
                    <!-- Logo Starts -->
                    <div class="logo">
                        <a data-toggle="collapse" data-target=".navbar-collapse.show" class="navbar-brand" href="index-product.html">
                            <!-- Logo White Starts -->
                            <img id="logo-light" class="logo-light" src="https://via.placeholder.com/159x28" alt="logo-light" />
                            <!-- Logo White Ends -->
                            <!-- Logo Black Starts -->
                            <img id="logo-dark" class="logo-dark" src="https://via.placeholder.com/159x28" alt="logo-dark" />
                            <!-- Logo Black Ends -->
                        </a>
                    </div>
                    <!-- Logo Ends -->

					<!-- Toggle Icon for Mobile Starts -->
					<button class="navbar-toggle navbar-toggler" type="button" data-toggle="collapse" data-target=".navbar-collapse" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span id="icon-toggler">
						  <span></span>
						  <span></span>
						  <span></span>
						  <span></span>
						</span>
					</button>
					<!-- Toggle Icon for Mobile Ends -->
					<div id="navbarSupportedContent" class="collapse navbar-collapse navbar-responsive-collapse">
						<!-- Main Menu Starts -->
						<ul class="nav navbar-nav" id="main-navigation">
                            @if(!Auth::user())
							<li class="active"><a href="/"><i class="fa fa-home"></i>{{ __('header.home') }}</a></li>
                            @else
							<li class="active"><a href="/"><i class="fa fa-home"></i>{{ __('header.logged_home') }}</a></li>
                            @endif
                            <li><a href="/rates"><i class="fa fa-calculator"></i>{{ __('header.rates') }}</a></li>
                            <li><a href="/rent_info"><i class="fa fa-question-circle"></i>{{ __('header.how_to_rent') }}</a></li>
                            <!--li><a href="/about"><i class="fa fa-user"></i>{{ __('header.faq') }}</a></li-->
                            <li><a href="/about"><i class="fa fa-newspaper-o"></i>{{ __('header.about_us') }}</a></li>
                            <!--li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-image"></i> portfolio <i class="fa fa-angle-down icon-angle"></i></a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="portfolio-2-columns.html">Portfolio 2 Columns</a></li>
									<li><a href="portfolio-3-columns.html">Portfolio 3 Columns</a></li>
									<li><a href="portfolio-4-columns.html">Portfolio 4 Columns</a></li>
									<li><a href="image-project.html">Image Project</a></li>
									<li><a href="slider-project.html">Slider Project</a></li>
									<li><a href="gallery-project.html">Gallery Project</a></li>
									<li><a href="video-project.html">Video project</a></li>
									<li><a href="youtube-project.html">youtube Project</a></li>
									<li><a href="vimeo-project.html">Vimeo Project</a></li>
								</ul>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-edit"></i> Blog <i class="fa fa-angle-down icon-angle"></i></a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="blog-right-sidebar.html">Right Sidebar</a></li>
									<li><a href="blog-left-sidebar.html">Left Sidebar</a></li>
									<li><a href="blog-grid-no-sidebar.html">Grid No Sidebar</a></li>
									<li><a href="blog-post.html">Single Post</a></li>
								</ul>
							</li-->

							<li><a href="/contacts"><i class="fa fa-envelope"></i>{{ __('header.contacts') }}</a></li>
							<!-- Cart Icon Starts -->
							<!--li class="cart hidden-xs hidden-sm"><a href="shopping-cart.html"><i class="fa fa-shopping-cart"></i></a></li -->
							<!-- Cart Icon Starts -->
							<!-- Search Icon Starts -->
							<!--li class="search hidden-xs hidden-sm"><button id="search-button" class="fa fa-search"></button></li-->
							<!-- Search Icon Ends -->
							@if(!Auth::user())
								<li ><a href="/login"><i class="fa fa-sign-in"></i>{{ __('header.login') }}</a></li>
							@else
								<li ><a href="/logout"><i class="fa fa-sign-in"></i>{{ __('header.logout') }}</a></li>
							@endif
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa Example of globe fa-globe" aria-hidden="true"></i>{{ __('header.choose_lang') }}<i class="fa fa-angle-down icon-angle"></i></a>
								<ul class="dropdown-menu" role="menu">
									<li><a href="?lang=ru">Русский</a></li>
									<li><a href="?lang=en">English</a></li>
								</ul>
							</li>
						</ul>
						<!-- Main Menu Ends -->
					</div>
					<!-- Search Input Starts -->
					<!--div class="site-search hidden-xs">
						<div class="search-container">
							<input id="search-input" type="text" placeholder="type your keyword and hit enter ...">
							<span class="close">×</span>
						</div>
					</div-->
					<!-- Search Input Ends -->
                    <!-- Navigation Menu Ends -->
                </nav>
                <!-- navbar Ends -->
            </div>
        </header>
 <!-- Header Ends -->
