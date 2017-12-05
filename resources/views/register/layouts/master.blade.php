
  <!DOCTYPE html>
  <html>
    <head>
      <title>@yield('title')</title>

      <!--Import Google Icon Font-->
      <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
      <link type="text/css" rel="stylesheet" href="{!! asset('cssfw/css/materialize.min.css') !!}"  media="screen,projection"/>

      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <style>
        html{
          font-size: 18px;
        }
        @yield('userstyle')
      </style>
       @yield('headscript')
    </head>

    <body>
      <!--header navigation-->
      <div class="navbar-fixed">
        <nav class="black" role="navigation">
          <div class="container">
              
              <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
              <ul id="nav-mobile" class="right hide-on-med-and-down">
               
                <li><a href="{!!route('login')!!}" class="waves-effect waves-light btn">Login</a></li>
              </ul>
              <ul class="side-nav" id="mobile-demo">
                
                <li><a href="{!!route('login')!!}" class="waves-effect waves-light btn">Login</a></li>
              </ul>
          </div>
        </nav>
      </div>
      <!--end of nav-->
      <div class="top"></div>
      @yield('content')
      <!--footer-->
      <footer class="page-footer grey darken-3">
          <div class="container">
            <div class="row">
              <div class="col s12">
                <div class="card-panel teal center-align">
                  <span class="white-text">
                    Testing api for FindaLoo
                  </span>
                </div>
              </div>
            </div>
            <div class="row">
            </div>
          </div>
          <div class="footer-copyright grey darken-4">
            <div class="container center-align">
              Copyright © {!!date('Y')!!} <a href="http://e-yantra.org" target="_blank">e-Yantra</a>. All rights reserved.
            </div>
          </div>
      </footer>
      <!--end of footer-->
      <!--Import jQuery before materialize.js-->
      <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
      <script type="text/javascript" src="{!! asset('cssfw/js/materialize.min.js') !!}"></script>
      @yield('addscriptlnk')
      <script>
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $( document ).ready(function(){
            $(".button-collapse").sideNav();
            @yield('readyscript')
        })
      </script>
      @yield('userscript')
    
      <noscript>
          <img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=1729245240668724&ev=PageView&noscript=1"
        />
      </noscript>
      <!-- End Facebook Pixel Code -->
    </body>
  </html>
