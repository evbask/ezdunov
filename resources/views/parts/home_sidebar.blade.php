<ul class="nav navbar-nav2 side-nav nicescroll-bar">

        <!-- User Profile -->
        <li>
            <div class="user-profile text-center">
                <img id="sidebar_avatar" src="{{asset('img/avatars/'.$user->avatar)}}" alt="user_auth" class="user-auth-img img-circle"/>
                <div class="dropdown mt-5">
                    <a href="#" class="dropdown-toggle pr-0 bg-transparent" data-toggle="dropdown">{{ $user->name}}<span class="caret"></span></a>
                    <ul class="dropdown-menu user-auth-dropdown" data-dropdown-in="flipInX" data-dropdown-out="flipOutX">
                        <li>
                            <a href="/settings"><i class="zmdi zmdi-settings"></i><span>{{ __('home_sidebar.settings') }}</span></a>
                        </li>

                        <li class="divider"></li>
                        <li>
                            <a href="/logout"><i class="zmdi zmdi-power"></i><span>{{ __('header.logout') }}</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </li>
        <!-- /User Profile -->
        <li class="navigation-header">
            {{--<i class="zmdi zmdi-more"></i>--}}
        </li>
        <li>
             <a href="/home"><div class="pull-left"><i class="icon-cursor mr-20"></i><span class="right-nav-text">{{ __('home_sidebar.main') }}</span></div><div class="clearfix"></div></a>
        </li>
        <li>
            <a href="javascript:void(0);" data-toggle="collapse" data-target="#ecom_dr"><div class="pull-left"><i class="icon-wallet mr-20"></i><span class="right-nav-text">{{ __('home_sidebar.balance') }}</span></div><div class="pull-right"><span class="label label-success">{{ $user->balance}}</span></div><div class="clearfix"></div></a>
        </li>
        <li>
            <a href=""><div class="pull-left"><i class="icon-chart icon-chart mr-20"></i><span class="right-nav-text">{{ __('home_sidebar.rate') }}</span></div><div class="pull-right"><span class="label label-warning">{{ $user->rate}}</span></div><div class="clearfix"></div></a>
        </li>
        <li>
            <a href="/rents"><div class="pull-left"><i class="icon-note mr-20"></i><span class="right-nav-text">{{ __('home_sidebar.my_rents') }}</span></div><div class="clearfix"></div></a>
        </li>
        <li>
            <a href="/chat"><div class="pull-left"><i class="icon-bubbles mr-20"></i><span class="right-nav-text">{{ __('home_sidebar.support') }}</span></div><div class="clearfix"></div></a>
        </li>
    </ul>

