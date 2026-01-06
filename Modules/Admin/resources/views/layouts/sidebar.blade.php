<style>
    .nav-sidebar li a i {
        margin-right: 11px;
    }

    .nav-sidebar .nav-link p {
        color: #000;
    }
</style>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="d-flex">
        <a href="{{ url('admin/dashboard') }}" class="brand-link left_sidebar_img">
            <img src="{{ url('assets/images/Frame.png') }}" alt="AdminLTE Logo" style="width:75px;" class="brand-image">
            &nbsp;<span class="brand-text font-weight-bold"><b>Privee</b></span>
        </a>
    </div>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Users Management -->
                <li
                    class="nav-item 
                    {{ request()->routeIs([
                        'admin.newuser',
                        'admin.rejectedUsers',
                        'admin.acceptUser',
                        'admin.ratedOutApplicants',
                        'admin.activeprofile',
                        'admin.nonActiveMembers',
                        'admin.viewuser',
                    ]) && request()->route('type') !== 'profileUpdateRequest'
                        ? 'menu-is-opening menu-open'
                        : '' }}">

                    <a href="#"
                        class="nav-link 
                        {{ request()->routeIs([
                            'admin.newuser',
                            'admin.rejectedUsers',
                            'admin.acceptUser',
                            'admin.ratedOutApplicants',
                            'admin.activeprofile',
                            'admin.nonActiveMembers',
                            'admin.viewuser',
                        ]) && request()->route('type') !== 'profileUpdateRequest'
                            ? 'active'
                            : '' }}">
                        <p class="d-flex align-items-center w-100">
                            <i class="fa-solid fa-users mr-2"></i>
                            Users Management
                            <i class="fas fa-angle-right right ml-auto"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview" style="background: #f8f9fa;">
                        <li class="nav-item">
                            <a href="{{ route('admin.newuser') }}"
                                class="nav-link {{ request()->routeIs('admin.newuser') ? 'active' : '' }}">
                                <i
                                    class="{{ request()->routeIs('admin.newuser') || request()->route('type') == 'newuser' ? 'fas' : 'far' }} far fa-circle nav-icon text-primary"></i>
                                <p>New Users</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.rejectedUsers') }}"
                                class="nav-link {{ request()->routeIs('admin.rejectedUsers') ? 'active' : '' }}">
                                <i
                                    class="{{ request()->routeIs('admin.rejectedUsers') || request()->route('type') == 'rejectedUsers' ? 'fas' : 'far' }} fa-circle nav-icon text-danger"></i>
                                <p>Rejected Users</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.acceptUser') }}"
                                class="nav-link {{ request()->routeIs('admin.acceptUser') ? 'active' : '' }}">
                                <i
                                    class="{{ request()->routeIs('admin.acceptUser') || request()->route('type') == 'acceptUser' ? 'fas' : 'far' }} fa-circle nav-icon text-warning"></i>
                                <p>New Applicants</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.ratedOutApplicants') }}"
                                class="nav-link {{ request()->routeIs('admin.ratedOutApplicants') ? 'active' : '' }}">
                                <i
                                    class="{{ request()->routeIs('admin.ratedOutApplicants') || request()->route('type') == 'ratedOutApplicants' ? 'fas' : 'far' }} fa-circle nav-icon text-info"></i>
                                <p>Rated Out Applicants</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.activeprofile') }}"
                                class="nav-link {{ request()->routeIs('admin.activeprofile') ? 'active' : '' }}">
                                <i
                                    class="{{ request()->routeIs('admin.activeprofile') || request()->route('type') == 'activeprofile' ? 'fas' : 'far' }} fa-circle nav-icon text-success"></i>
                                <p>Active Members</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.nonActiveMembers') }}"
                                class="nav-link {{ request()->routeIs('admin.nonActiveMembers') ? 'active' : '' }}">
                                <i
                                    class="{{ request()->routeIs('admin.nonActiveMembers') || request()->route('type') == 'nonActiveMembers' ? 'fas' : 'far' }} fa-circle nav-icon text-secondary"></i>
                                <p>Non Active Members</p>
                            </a>
                        </li>

                        <!--<li class="nav-item">-->
                        <!--    <a href="{{ route('admin.ratedIn') }}" class="nav-link {{ request()->routeIs('admin.ratedIn') ? 'active' : '' }}">-->
                        <!--        <i class="far fa-circle nav-icon text-success"></i>-->
                        <!--        <p>Rated IN (Approved)</p>-->
                        <!--    </a>-->
                        <!--</li>-->


                        <!-- <li class="nav-item">-->
                        <!--    <a href="{{ route('admin.deniedUser') }}" class="nav-link {{ request()->routeIs('admin.deniedUser') ? 'active' : '' }}">-->
                        <!--        <i class="far fa-circle nav-icon text-danger"></i>-->
                        <!--        <p>Denied by Admin</p>-->
                        <!--    </a>-->
                        <!--</li>-->





                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.profile.update.request') }}"
                        class="nav-link {{ request()->routeIs('admin.profile.update.request') || request()->route('type') == 'profileUpdateRequest' ? 'active' : '' }}">
                        <i class="fas fa-image"></i>
                        <p>Photo Update Request</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.reported.users') }}"
                        class="nav-link {{ request()->routeIs('admin.reported.users') ? 'active' : '' }}">
                        <i class="fas fa-flag"></i>
                        <p>Reported Users</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.support') }}"
                        class="nav-link {{ request()->routeIs('admin.support') ? 'active' : '' }}">
                        <i class="fas fa-headset"></i>
                        <p>Support Requests</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.help.us.do.better') }}"
                        class="nav-link {{ request()->routeIs('admin.help.us.do.better') ? 'active' : '' }}">
                        <i class="fas fa-lightbulb"></i>
                        <p>Help us to do better</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.user.search') }}"
                        class="nav-link {{ request()->routeIs('admin.user.search') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <p>Statistics</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.location.management') }}"
                        class="nav-link {{ request()->routeIs(['admin.location.management', 'admin.add.update.country', 'admin.add.update.region', 'admin.add.update.city']) ? 'active' : '' }}">
                        <i class="fas fa-globe"></i>
                        <p>Location Management</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.nationalities.management') }}"
                        class="nav-link {{ request()->routeIs(['admin.nationalities.management', 'admin.add.update.nationality']) ? 'active' : '' }}">
                        <i class="fas fa-flag"></i>
                        <p>Nationalities</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.ready.members') }}"
                        class="nav-link {{ request()->routeIs('admin.ready.members') || request()->routeIs('admin.add.ready.member') || request()->routeIs('admin.edit.ready.member') || request()->routeIs('admin.view.ready.member') ? 'active' : '' }}">
                        <i class="fas fa-user-plus"></i>
                        <p>Ready Members</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.send.notification') }}"
                        class="nav-link {{ request()->routeIs('admin.send.notification') ? 'active' : '' }}">
                        <i class="fas fa-paper-plane"></i>
                        <p>Send Notifications</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.custom-options.index') }}"
                        class="nav-link {{ request()->routeIs('admin.custom-options.index') ? 'active' : '' }}">
                        <i class="fa fa-cogs" aria-hidden="true"></i>
                        <p>Custom Options</p>
                    </a>
                </li>
                <!--<li class="nav-item">-->
                <!--    <a href="{{ route('admin.report.reason') }}" class="nav-link {{ Request::is('admin.report.reason') ? 'active' : '' }}">-->
                <!--        <i class="fas fa-ban"></i>-->
                <!--        <p>Report Reasons</p>-->
                <!--    </a>-->
                <!--</li>-->

                <!--setings-->
                <li
                    class="nav-item 
                {{ request()->routeIs(['admin.term', 'admin.privacy', 'admin.report.reason', 'admin.add.update.reasons', 'admin.timer', 'admin.change.password']) ? 'menu-open' : '' }}">

                    <a href="#"
                        class="nav-link 
                    {{ request()->routeIs(['admin.term', 'admin.privacy', 'admin.report.reason', 'admin.add.update.reasons', 'admin.timer', 'admin.change.password']) ? 'active' : '' }}">
                        <p class="d-flex align-items-center w-100">
                            <i class="fa-solid fa-gear"></i>
                            Settings
                            <i class="fas fa-angle-right right ml-auto"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview" style="background: #f8f9fa;">
                        <li class="nav-item">
                            <a href="{{ route('admin.term') }}"
                                class="nav-link {{ request()->routeIs('admin.term') ? 'active' : '' }}">
                                <i
                                    class="{{ request()->routeIs('admin.term') ? 'fas' : 'far' }} fa-circle nav-icon text-danger"></i>
                                <p>Terms and Conditions</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.privacy') }}"
                                class="nav-link {{ request()->routeIs('admin.privacy') ? 'active' : '' }}">
                                <i
                                    class="{{ request()->routeIs('admin.privacy') ? 'fas' : 'far' }} fa-circle nav-icon text-danger"></i>
                                <p>Privacy Policy</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.report.reason') }}"
                                class="nav-link {{ request()->routeIs(['admin.report.reason', 'admin.add.update.reasons']) ? 'active' : '' }}">
                                <i
                                    class="{{ request()->routeIs('admin.report.reason') ? 'fas' : 'far' }} fa-circle nav-icon text-danger"></i>
                                <p>Report Reasons</p>
                            </a>
                        </li>




                        <li class="nav-item">
                            <a href="{{ route('admin.timer') }}"
                                class="nav-link {{ request()->routeIs('admin.timer') ? 'active' : '' }}">
                                <i
                                    class="{{ request()->routeIs('admin.timer') ? 'fas' : 'far' }} fa-circle nav-icon text-danger"></i>
                                <p>Rating period</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('admin.change.password') }}"
                                class="nav-link {{ request()->routeIs('admin.change.password') ? 'active' : '' }}">
                                <i
                                    class="{{ request()->routeIs('admin.change.password') ? 'fas' : 'far' }} fa-circle nav-icon text-danger"></i>
                                <p>Change Password</p>
                            </a>
                        </li>




                    </ul>
                </li>


                <!-- Logout -->
                <li class="nav-item">
                    <a href="{{ route('admin.logout') }}"
                        class="nav-link {{ request()->routeIs('admin.logout') ? 'active' : '' }}">
                        <i class="fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
