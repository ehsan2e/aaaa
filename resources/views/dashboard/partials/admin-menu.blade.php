{{--{{ print_r(\App\Facades\UIManager::getActivePath(),true) }}--}}
<li class="nav-item dropdown @inactivepath('crm', 'active')">
    <a id="crmDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        {{ __('CRM') }} <span class="caret"></span>
    </a>

    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="crmDropdown">
        <a class="dropdown-item @inactivepath('clients', 'active')" href="{{ route('dashboard.admin.crm.client.index') }}">{{ __('Clients') }}</a>
        <a class="dropdown-item @inactivepath('support-ticket', 'active')" href="{{ /*route('dashboard.admin.crm.post-category.index')*/ '#' }}">{{ __('Support Ticket') }}</a>
    </div>
</li>
<li class="nav-item dropdown">
    <a id="clientDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        {{ __('Sold Items') }} <span class="caret"></span>
    </a>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="clientDropdown">
        <a class="dropdown-item" href="#">{{ __('Boxes') }}</a>
        <a class="dropdown-item" href="#">{{ __('Products') }}</a>
        <a class="dropdown-item" href="#">{{ __('Services') }}</a>
    </div>
</li>
<li class="nav-item dropdown @inactivepath('catalog', 'active')">
    <a id="catalogDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        {{ __('Catalog') }} <span class="caret"></span>
    </a>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="catalogDropdown">
        <a class="dropdown-item @inactivepath(config('nova.box_category_code') . '-category', 'active')" href="{{ route('dashboard.admin.catalog.product-type.index', ['category_code' => config('nova.box_category_code')]) }}">{{ __('Box Types') }}</a>
        <a class="dropdown-item @inactivepath('product-categories', 'active')" href="{{ route('dashboard.admin.catalog.product-category.index') }}">{{ __('Categories') }}</a>
        <a class="dropdown-item @inactivepath(['any-product-category', '-category'], 'active')" href="{{ route('dashboard.admin.catalog.product-type.index') }}">{{ __('Product Types') }}</a>
        <a class="dropdown-item @inactivepath(config('nova.box_service_category_code') . '-category', 'active')" href="{{ route('dashboard.admin.catalog.product-type.index', ['category_code' => config('nova.box_service_category_code')]) }}">{{ __('Service Types') }}</a>
        <a class="dropdown-item @inactivepath('suppliers', 'active')" href="{{ route('dashboard.admin.catalog.supplier.index') }}">{{ __('Suppliers') }}</a>
    </div>
</li>
<li class="nav-item dropdown">
    <a id="salesDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        {{ __('Sales') }} <span class="caret"></span>
    </a>

    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="salesDropdown">
        <a class="dropdown-item" href="#">{{ __('Invoices') }}</a>
        <a class="dropdown-item" href="#">{{ __('Orders') }}</a>
        <a class="dropdown-item" href="#">{{ __('Payments') }}</a>
    </div>
</li>
<li class="nav-item dropdown @inactivepath('cms', 'active')">
    <a id="cmsDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        {{ __('CMS') }} <span class="caret"></span>
    </a>

    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="cmsDropdown">
        <a class="dropdown-item @inactivepath('post-categories', 'active')" href="{{ route('dashboard.admin.cms.post-category.index') }}">{{ __('Categories') }}</a>
        <a class="dropdown-item @inactivepath('gallery', 'active')" href="{{ route('dashboard.admin.gallery.index') }}">{{ __('Gallery') }}</a>
        <a class="dropdown-item @inactivepath('posts', 'active')" href="{{ route('dashboard.admin.cms.post.index') }}">{{ __('Posts') }}</a>
        <a class="dropdown-item @inactivepath('custom-urls', 'active')" href="{{ route('dashboard.admin.cms.custom-url.index') }}">{{ __('Urls') }}</a>
    </div>
</li>