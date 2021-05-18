 
            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Menu
            </div>
            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ Request::segment(1) == 'dashboard' ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item {{ in_array(Request::segment(1), ['penjualan']) ? 'active' : '' }}">
                <a class="nav-link {{ in_array(Request::segment(1), ['penjualan']) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-coins"></i>
                    <span>Penjualan</span>
                </a>
                <div id="collapseTwo" class="collapse {{ Helper::menu_expand() != "yes" ? (in_array(Request::segment(1), ['penjualan']) ? 'show' : '') : '' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded"> 
                        <a class="collapse-item {{ in_array(Request::segment(1), ['penjualan']) ? 'active' : '' }}" href="{{url('penjualan/skpp')}}">SKPP</a>
                        {{-- <a class="collapse-item" href="{{url('faktur_penjualan')}}">Faktur Penjualan</a>
                        <a class="collapse-item" href="{{url('surat_jalan')}}">Surat jalan</a> --}}
                    </div>
                </div>
            </li>
            <!-- Nav Item - Dashboard --> 
            <li class="nav-item {{ in_array(Request::segment(1), ['pembelian']) ? 'active' : '' }}">
                <a class="nav-link {{ in_array(Request::segment(1), ['pembelian']) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseThree"
                    aria-expanded="true" aria-controls="collapseThree">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Pembelian</span>
                </a>
                <div id="collapseThree" class="collapse {{ Helper::menu_expand() != "yes" ? (in_array(Request::segment(1), ['pembelian']) ? 'show' : '') : '' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">  
                        <a class="collapse-item {{ in_array(Request::segment(1), ['pembelian']) ? 'active' : '' }}" href="{{url('pembelian/pre_order')}}">Pre Order</a> 
                    </div>
                </div>
            </li>
 
            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ in_array(Request::segment(1), ['stok']) ? 'active' : '' }}">
                <a class="nav-link" href="{{url('stok')}}">
                    <i class="fas fa-fw fa-boxes"></i>
                    <span>Stok gudang</span></a>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item {{ in_array(Request::segment(1), ['customer','produsen','produk','gudang','user','supir','profil_perusahaan']) ? 'active' : '' }}">
                <a class="nav-link {{ in_array(Request::segment(1), ['customer','produsen','produk','gudang','user','supir','profil_perusahaan']) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseDataMaster"
                    aria-expanded="true" aria-controls="collapseDataMaster">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Data master</span>
                </a>
                <div id="collapseDataMaster" class="collapse {{ Helper::menu_expand() != "yes" ? (in_array(Request::segment(1), ['customer','produsen','produk','gudang','user','supir','profil_perusahaan']) ? 'show' : '') : '' }}" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded"> 
                        <a class="collapse-item {{ Request::segment(1) == 'customer' ? 'active' : '' }}" href="{{url('customer')}}">Customer</a>
                        <a class="collapse-item {{ Request::segment(1) == 'produsen' ? 'active' : '' }}" href="{{url('produsen')}}">Produsen</a>
                        <a class="collapse-item {{ Request::segment(1) == 'produk' ? 'active' : '' }}" href="{{url('produk')}}">Produk</a>
                        <a class="collapse-item {{ Request::segment(1) == 'gudang' ? 'active' : '' }}" href="{{url('gudang')}}">Gudang</a>
                        <a class="collapse-item {{ Request::segment(1) == 'supir' ? 'active' : '' }}" href="{{url('supir')}}">Supir</a>
                        <a class="collapse-item {{ Request::segment(1) == 'user' ? 'active' : '' }}" href="{{url('user')}}">User</a>
                        <a class="collapse-item {{ Request::segment(1) == 'profil_perusahaan' ? 'active' : '' }}" href="{{url('profil_perusahaan')}}">Profil perusahaan</a>
                    </div>
                </div>
            </li>
 