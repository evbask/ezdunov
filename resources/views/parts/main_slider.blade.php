 <!-- Main Slider Section Starts -->
<section class="mainslider" id="mainslider" >
            <!-- Slider Hero Starts -->
            <!--img src="/img/backgrounds/km@php echo rand(1,4); @endphp.jpg"  alt=""  class="bg-image hidden-lg hidden-sm" data-no-retina-->
            <div class="rev_slider_wrapper fullwidthbanner-container dark-slider" data-alias="vimeo-hero" style="margin:0px auto;background-color:transparent;padding:0px;margin-top:0px;margin-bottom:0px;">
                <!-- START REVOLUTION SLIDER 5.0.7 fullwidth mode -->
                <div id="rev_slider_vimeo" class="rev_slider fullwidthabanner" style="display:none;" data-version="5.0.7">
                    <ul>
                        <!-- SLIDE  -->
                        <li data-index="rs-235" data-transition="fade" data-slotamount="default" data-easein="default" data-easeout="default" data-masterspeed="1500" data-rotate="0" data-saveperformance="off" data-title="Intro" data-description="">
                            <!-- MAIN IMAGE -->
                            <!--div class=" rev-slidebg hidden-sm hidden-lg rs-parallaxlevel-0" data-bgposition="center center" data-bgimage="/img/backgrounds/km@php echo rand(1,4); @endphp.jpg" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="5"-->
                           <img data-id="background_1" src="/img/backgrounds/km@php echo rand(1,4); @endphp.webp" alt="" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="5"  class="rev-slidebg hidden-lg hidden-sm" data-no-retina>
                            <!--/div-->
                            <!-- LAYERS -->

                            <!-- LAYER NR. 1 -->
                            <!--div class="tp-caption tp-resizeme rs-parallaxlevel-1" id="slide-235-layer-1" data-x="['right','right','center','center']" data-hoffset="['-254','-453','70','60']" data-y="['middle','middle','middle','bottom']" data-voffset="['50','50','211','25']" data-width="none" data-height="none" data-whitespace="nowrap" data-transform_idle="o:1;" data-transform_in="x:right;s:1500;e:Power3.easeOut;" data-transform_out="opacity:0;s:1500;e:Power4.easeIn;s:1500;e:Power4.easeIn;" data-start="2500" data-responsive_offset="on" style="z-index: 5;"><img src="img/revolution-slider/product/mijia1.png" alt="" width="583" height="583" data-ww="['583px','583px','500px','350px']" data-hh="['583px','583px','500px','350px']" data-no-retina>
                            </div-->
                            @php 
                                $browser = session('browser');
                            @endphp
                            @if (!$browser['ismobiledevice'])
                            <div class="rev-slidebg hidden-xs " data-bgposition="center center" data-bgfit="cover" data-bgparallax="5">
                                <video id="background-video"   loop autoplay="autoplay" muted>
                                </video>
                            </div>
                            <script>

                                $(document).ready(function(){
                                    let video =  '<source src="img/shutterstock_1016149003.mp4" type="video/mp4; codecs="avc1.42E01E, mp4a.40.2"">';
                                    let video2 = '<source src="img/shutterstock_1016149003.webm" type="video/webm">';
                                    $('#background-video').html(video+video2);
                                });

                                var vid = document.getElementById("background-video"); 
                                var playPromise = vid.play();
                                vid.playbackRate = 0.5;
                                if (playPromise !== undefined) {
                                    playPromise.then(_ => {
                                    // Automatic playback started!
                                    // Show playing UI.
                                        vid.play();  
                                    })
                                    .catch(error => {
                                    // Auto-play was prevented
                                    // Show paused UI.
                                        vid.play();  
                                    });
                                }
                                function setFirstSectionHeight(){
                                    let width = window.innerWidth;
                                    if (width >= 768 && width <1562) {
                                        document.getElementById("mainslider").setAttribute("style","height:"+(vid.clientHeight-171)+"px");
                                    } else {
                                        document.getElementById("mainslider").setAttribute("style", "");
                                    }
                                }
                                window.onload = function () {
                                    $(".slotholder").addClass('hidden-lg hidden-sm');

                                    setFirstSectionHeight();
                                };
                                window.onresize = function () {
                                    let width = window.innerWidth;
                                    setFirstSectionHeight();
                                }
                            </script>
                            @endif
                            <!-- LAYER NR. 2 -->
                            <!--div class="tp-caption   tp-resizeme rs-parallaxlevel-1" id="slide-235-layer-2" data-x="['right','right','center','center']" data-hoffset="['-254','-254','200','200']" data-y="['top','top','top','bottom']" data-voffset="['-217','-216','580','63']" data-width="none" data-height="none" data-whitespace="nowrap" data-transform_idle="o:1;" 
                            data-transform_in="x:right;s:1500;e:Power3.easeOut;"
                             data-data-data="z:0;rX:0deg;rY:0;rZ:0;sX:1.5;sY:1.5;skX:0;skY:0;opacity:0;s:1500;e:Power3.easeOut;" 
                            data-transform_out="opacity:0;s:1500;e:Power4.easeIn;s:1500;e:Power4.easeIn;" data-mask_in="x:0px;y:0px;s:inherit;e:inherit;" data-start="2500" data-responsive_offset="on" style="z-index: 6;"><img src="img/revolution-slider/product/mijia2.png" alt="" 
                            width="696" height="962" 
                            data-ww="['696px','696px','330px','230px']" data-hh="['962px','962px','457px','319px']" data-no-retina>
                            </div -->

                            <!-- LAYER NR. 1 -->
                            <!-- <div class="caption-bg rs-parallaxlevel-1"> -->
                            <!-- LAYER NR. 1 -->
                            <div class="tp-caption NotGeneric-Title   tp-resizeme rs-parallaxlevel-0" data-frames='[{"from":"x:[105%];z:0;rX:45deg;rY:0deg;rZ:90deg;sX:1;sY:1;skX:0;skY:0;","mask":"x:0px;y:0px;s:inherit;e:inherit;","speed":2000,"to":"o:1;","delay":1000,"split":"chars","splitdelay":0.05,"ease":"Power4.easeInOut"},{"delay":"wait","speed":1000,"to":"y:[100%];","mask":"x:inherit;y:inherit;s:inherit;e:inherit;","ease":"Power2.easeInOut"}]' data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']" data-fontsize="['70','70','40','40']" data-lineheight="['70','70','70','50']" data-width="none" data-height="none" data-whitespace="nowrap" data-transform_idle="o:1;" data-transform_in="x:[105%];z:0;rX:45deg;rY:0deg;rZ:90deg;sX:1;sY:1;skX:0;skY:0;s:2000;e:Power4.easeInOut;" data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" data-mask_in="x:0px;y:0px;s:inherit;e:inherit;" data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" data-start="1000" data-splitin="chars" data-splitout="none" data-responsive_offset="on" data-elementdelay="0.05" style="z-index: 5; white-space: nowrap;">
                            <div class="NotGeneric-Title-Main" style="padding-top: 10px; padding-bottom: 10px; padding-left: 50px; padding-right: 50px;">{{ __('index.ezdunov') }}</div>
                            </div>

                            <!-- LAYER NR. 2 -->
                            <div class="tp-caption NotGeneric-SubTitle   tp-resizeme rs-parallaxlevel-0" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['middle','middle','middle','middle']" data-voffset="['80','80','80','80']" data-width="none" data-height="none" data-whitespace="nowrap" data-transform_idle="o:1;" data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;" data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" data-fontsize="['30','30','30','20']" data-lineheight="['40','40','40','20']" data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" data-start="1500" data-splitin="none" data-splitout="none" data-responsive_offset="on" style="z-index: 6; white-space: nowrap; font-weight: 400;">
                            <div class="" style="width: fit-content; margin: 0 auto; padding-top: 5px; font-weight: 400; padding-bottom: 5px; padding-left: 50px; padding-right: 50px;">{{ __('index.header_title') }}</div>
                            </div>

                            <!-- LAYER NR. 3 -->
                            <div class="tp-caption NotGeneric-Icon   tp-resizeme rs-parallaxlevel-0" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['middle','middle','middle','middle']" data-voffset="['-68','-68','-68','-68']" data-width="none" data-height="none" data-whitespace="nowrap" data-transform_idle="o:1;" data-style_hover="cursor:default;" data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:1500;e:Power4.easeInOut;" data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" data-start="2000" data-splitin="none" data-splitout="none" data-responsive_offset="on" style="z-index: 7; white-space: nowrap;"><i class="pe-7s-refresh"></i>
                            </div>
                            <!-- LAYER NR. 4 -->
                            <div class="tp-caption" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['middle','middle','middle','middle']" data-voffset="['150','150','150','150']" data-width="none" data-height="none" data-whitespace="nowrap" data-transform_idle="o:1;" data-transform_hover="o:1;rX:0;rY:0;rZ:0;z:0;s:300;e:Power1.easeInOut;" data-style_hover="c:rgba(255, 255, 255, 1.00);bc:rgba(255, 255, 255, 1.00);" data-transform_in="y:100px;sX:1;sY:1;opacity:0;s:2000;e:Power3.easeInOut;" data-transform_out="y:50px;opacity:0;s:1000;e:Power2.easeInOut;" data-start="750" data-splitin="none" data-splitout="none" data-responsive_offset="on" data-responsive="off" style="z-index: 11; white-space: nowrap;outline:none;box-shadow:none;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;cursor:pointer;"><a href="/register" class="custom-button slider-button scroll-to-target">{{ __('index.register_link') }}</a></div>
                        
                            <!--div class="tp-caption NotGeneric-Title   tp-resizeme rs-parallaxlevel-1" data-frames='[{"from":"x:[105%];z:0;rX:45deg;rY:0deg;rZ:90deg;sX:1;sY:1;skX:0;skY:0;","mask":"x:0px;y:0px;s:inherit;e:inherit;","speed":2000,"to":"o:1;","delay":1000,"split":"chars","splitdelay":0.05,"ease":"Power4.easeInOut"},{"delay":"wait","speed":1000,"to":"y:inherit;","mask":"x:inherit;y:inherit;s:inherit;e:inherit;","ease":"Power2.easeInOut"}]' data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']" data-fontsize="['70','70','70','45']" data-lineheight="['70','70','70','50']" data-width="none" data-height="none" data-whitespace="nowrap" data-transform_idle="o:1;" data-transform_in="x:[105%];z:0;rX:45deg;rY:0deg;rZ:90deg;sX:1;sY:1;skX:0;skY:0;s:2000;e:Power4.easeInOut;" data-transform_out="y:[50%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" data-mask_in="x:0px;y:0px;s:inherit;e:inherit;" data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" data-start="1000" data-splitin="chars" data-splitout="none" data-responsive_offset="on" data-elementdelay="0.05" style="z-index: 5; white-space: nowrap; text-align: center; padding: 0px; margin-bottom: 8px;">
                                <div class="NotGeneric-Title-Main" style="padding-top: 10px; padding-bottom: 10px; padding-left: 50px; padding-right: 50px;">{{ __('index.ezdunov') }}</div><br/>
                            </div>
                            <style>
                                .tp-splitted.tp-linesplit{
                                    text-align: center!important;
                                }
                                .tp-parallax-wrap{
                                    visibility: initial!important;
                                }
                            </style-->

                            <!-- LAYER NR. 2 -->
                            <!--div class="tp-caption NotGeneric-SubTitle   tp-resizeme rs-parallaxlevel-1" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['middle','middle','middle','middle']" data-voffset="['52','52','52','51']" data-width="none" data-height="none" data-whitespace="nowrap" data-transform_idle="o:1;" data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:2000;e:Power4.easeInOut;" data-fontsize="['40','30','30','20']" data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" data-start="1500" data-splitin="none" data-splitout="none" data-responsive_offset="on" style="z-index: 6; white-space: nowrap;">
                                <div class="" style="width: fit-content; margin: 0 auto; padding-top: 5px; padding-bottom: 5px; padding-left: 50px; padding-right: 50px;">{{ __('index.header_title') }}</div>
                          
                          </div-->

                            

                            <!-- LAYER NR. 3 -->
                            <!--div class="tp-caption NotGeneric-Icon   tp-resizeme rs-parallaxlevel-0" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['middle','middle','middle','middle']" data-voffset="['-68','-68','-68','-68']" data-width="none" data-height="none" data-whitespace="nowrap" data-transform_idle="o:1;" data-style_hover="cursor:default;" data-transform_in="y:[100%];z:0;rX:0deg;rY:0;rZ:0;sX:1;sY:1;skX:0;skY:0;opacity:0;s:1500;e:Power4.easeInOut;" data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;" data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;" data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;" data-start="2000" data-splitin="none" data-splitout="none" data-responsive_offset="on" style="z-index: 7; white-space: nowrap;"><i class="pe-7s-refresh"></i>
                            </div-->
                            <!-- </div> -->
                            <!-- LAYER NR. 4 -->
                            <!--div class="tp-caption" data-x="['center','center','center','center']" data-hoffset="['0','0','0','0']" data-y="['middle','middle','middle','middle']" data-voffset="['150','150','150','150']" data-width="none" data-height="none" data-whitespace="nowrap" data-transform_idle="o:1;" data-transform_hover="o:1;rX:0;rY:0;rZ:0;z:0;s:300;e:Power1.easeInOut;" data-style_hover="c:rgba(255, 255, 255, 1.00);bc:rgba(255, 255, 255, 1.00);" data-transform_in="y:100px;sX:1;sY:1;opacity:0;s:2000;e:Power3.easeInOut;" data-transform_out="y:50px;opacity:0;s:1000;e:Power2.easeInOut;" data-start="750" data-splitin="none" data-splitout="none" data-responsive_offset="on" data-responsive="off" style="z-index: 11; white-space: nowrap;outline:none;box-shadow:none;box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;cursor:pointer;"><a href="#about" class="custom-button slider-button scroll-to-target">learn more about us</a></div-->
                        
                        </li>
                    </ul>
                    <div class="tp-static-layers">
                    </div>
                        
                </div>
            </div>
            <!-- END REVOLUTION SLIDER -->
            <!-- Slider Hero Ends -->
        </section>
        <!-- Main Slider Section Ends -->
