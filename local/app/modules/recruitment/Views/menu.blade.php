<aside class="main-sidebar" ng-controller="MenuController">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <ul class="sidebar-menu">
            <li>
                <a href="{{URL::to('/recruitment')}}">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @include('HRM::Partial_view.partial_menu',['menus'=>config('menu.recruitment')])
        </ul>
		<ul class="sidebar-menu">
               
            @if((auth()->user()->type ==11) || ((auth()->user()->type == 44) && (auth()->user()->id == 356 || auth()->user()->id ==357 || auth()->user()->id ==367 || auth()->user()->id == 355 )))

            <li>
                <a href="{{URL::to('/recruitment/applicant/list')}}">
                    <i class="fa fa-users"></i>
                    <span>Applicant List</span>
                </a>
            </li>
            
                        
             @endif
        </ul>
    </section>
    <!-- /.sidebar -->
    <script>
        $(window).load(function(){
            var l = $('.sidebar-menu').children('li');
            function removeMenu(m){

                m.each(function () {
                    //console.log({parent: $.trim($(this).parents('li').eq($(this).parents('li').length-1).children('a').text()),children: m.text()})
                    //alert($(this).children('ul').length+" "+$(this).children('ul').children('li').length)
                    if($(this).children('ul').length>0) {
                        if ($(this).children('ul').children('li').length > 0) {
                            removeMenu($(this).children('ul').children('li'));
                        }
                        else if ($(this).children('ul').children('li').length <= 0) {
                            // alert(m.length)
                            $(this).remove();
                        }
                    }
                })
            }
            removeMenu(l)
        })
    </script>
</aside>