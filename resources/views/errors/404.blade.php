<!DOCTYPE html>
<html lang="en">

@include('common.head', ['nolayers' => true, 'nonavigation' => true,  'page_title' => __('404.page_title'), 'page_description' => __('404.page_description'), 'page_keywords' => __('404.page_keywords')])
<style>
.bg-no-repeat {
    background-repeat: no-repeat;
}
.bg-cover {
    background-size: cover;
}
.pin {
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}
@media (max-width: 767px){
    body.error-page .error>div.right{
        position: absolute;
        z-index: 1;
        width: 100%;
    }
    body.error-page .error>div.left{
        width: 100%;
        position: absolute;
        z-index: 2;
        background: rgba(17, 17, 17, .7);
    }
}
@media (min-width: 768px){
    .md\:bg-left {
        background-position: left;
    }
}
@media (min-width: 992px){
    .lg\:bg-center {
        background-position: center;
    }
}
</style>
<body class="double-diagonal dark error-page">
    <!-- Preloader Starts -->
    <div class="preloader" id="preloader">
        <div class="logopreloader">
            <img src="http://via.placeholder.com/159x28" alt="logo">
        </div>
        <div class="loader" id="loader"></div>
    </div>
    <!-- Preloader Ends -->
    <!-- Page Wrapper Starts -->
    <div class="wrapper">
        <div class="container-fluid error">
			<div class="left">
				<div class="text-center">
					<!-- Logo Starts -->
					<a class="logo" href="/">
						<img class="img-responsive" src="http://via.placeholder.com/159x28" alt="logo">
					</a>
					<!-- Logo Ends -->
					<!-- Error 404 Content Starts -->
					<div class="big-404">404</div>
					<h3>{{ __('404.oops') }}</h3>
                    <p>{{ __('404.info') }}</p>
					<a class="custom-button" href="/">{{ __('404.home_button') }}</a>
					<!-- Error 404 Content Starts -->
				</div>
			</div><div class="right">
                <div style="background-image: url('/svg/404.svg');" class="pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
                </div>
            </div>
		</div>
    </div>
    <!-- Wrapper Ends -->

    <!-- Template JS Files -->
    <script src="/js/jquery-2.2.4.min.js"></script>
    <script src="/js/plugins/jquery.easing.1.3.js"></script>
    <script src="/js/plugins/bootstrap.min.js"></script>

    <!-- Main JS Initialization File -->
    <script src="/js/custom.js"></script>

</body>

</html>