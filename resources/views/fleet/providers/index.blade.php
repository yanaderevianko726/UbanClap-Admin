@extends('fleet.layout.base')

@section('title', 'Providers ')

@section('content')
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <h5 class="mb-1">
                @lang('admin.provides.providers')
            </h5>
            <form action="{{ route('fleet.provider.index') }}" method="get">
                <div class="columns">
                    <div class="mb-1 col-md-3">
                        <input name="name" type="text" class="input" placeholder="Nome do @lang('admin.provider')" aria-label="Nome do @lang('admin.provider')" aria-describedby="basic-addon2">
                    </div>
                    <div class="column">
                        <label class="radio-inline">
                            <input type="radio" name="status" value="A" class="radio"{{ request()->has('status')&&request()->get('status')=="A"?" checked":"" }}> Regularizados
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="status" value="P" class="radio"{{ request()->has('status')&&request()->get('status')=="P"?" checked":"" }}> Docs Pendentes
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="status" value="F" class="radio"{{ request()->has('status')&&request()->get('status')=="F"?" checked":"" }}> Falta Docs
                        </label>
                    </div>
                    <div class="mb-1 col-md-1">
                        <button class="button is-link" type="submit">Search</button>
                    </div>
                </div>
            </form>
            @can('provider-create')
                <a href="{{ route('admin.provider.create') }}" style="margin-left: 1em;" class="button is-link pull-right"><i class="fa fa-plus"></i>@lang('admin.provides.add_new_provider')</a>
            @endcan
            <br>
            <a href="{{ route('fleet.provider.create') }}" style="margin-left: 1em;" class="button is-link pull-right"><i class="fa fa-plus"></i> @lang('admin.provides.add_new_provider')</a>
            <div class="table-container">
                                    <table class="table is-responsive is-fullwidth is-striped">
                <thead>
                    <tr>
                        <th>@lang('admin.id')</th>
                        <th>@lang('admin.provides.full_name')</th>
                        <th>@lang('admin.email')</th>
                        <th>@lang('admin.mobile')</th>
                        <th>@lang('admin.users.Wallet_Amount')</th>
                        <th>@lang('admin.provides.total_requests')</th>
                        <th>@lang('admin.provides.accepted_requests')</th>
                        <th>@lang('admin.provides.service_type')</th>
                        <th>@lang('admin.provides.online')</th>
                        <th>@lang('admin.action')</th>
                    </tr>
                </thead>
                <tbody>
                @php($page = ($pagination->currentPage-1)*$pagination->perPage)
                @foreach($providers as $index => $provider)
                @php($page++)
                    <tr>
                        <td>{{ $page }}</td>
                        <td>{{ $provider->first_name }} {{ $provider->last_name }}</td>
                        @if(Setting::get('demo_mode', 0) == 1)
                        <td>{{ substr($provider->email, 0, 3).'****'.substr($provider->email, strpos($provider->email, "@")) }}</td>
                        @else
                        <td>{{ $provider->email }}</td>
                        @endif
                        @if(Setting::get('demo_mode', 0) == 1)
                        <td>+919876543210</td>
                        @else
                        <td>{{ $provider->mobile }}</td>
                        @endif
                        <td>
                            @if($provider->wallet_balance < 0)
                                <label style="cursor: default;" class="btn small btn-block btn-danger">{{ currency($provider->wallet_balance) }}</label>
                            @elseif($provider->wallet_balance == 0)
                                <label style="cursor: default;" class="btn small btn-block btn-warning">{{ currency($provider->wallet_balance) }}</label>
                            @else
                                <label style="cursor: default;" class="btn small btn-block btn-success">{{ currency($provider->wallet_balance) }}</label>
                            @endif
                        </td>
                        <td>{{ $provider->total_requests() }}</td>
                        <td>{{ $provider->accepted_requests() }}</td>
                        <td>
                            @if($provider->active_documents() == $total_documents && $provider->service != null)
                                <a class="button is-success btn-block" href="{{route('fleet.provider.document.index', $provider->id )}}">Verificado</a>
                            @else
                                <a class="button  is-danger is-link btn-block label-right" href="{{route('fleet.provider.document.index', $provider->id )}}">Pendente <span class="btn-label">{{ $provider->pending_documents() }}</span></a>
                            @endif
                        </td>
                        <td>
                            @if($provider->service)
                                @if($provider->service->status == 'active')
                                    <label class="button is-block btn-primary">Sim</label>
                                @else
                                    <label class="button is-block btn-warning">N??o</label>
                                @endif
                            @else
                                <label class="button is-block btn-danger">N/A</label>
                            @endif
                        </td>
                        <td>
                            <div class="input-group-btn">
                                @if($provider->status == 'approved')
                                <a class="button  is-danger is-link btn-block" href="{{ route('fleet.provider.disapprove', $provider->id ) }}">Disable</a>
                                @else
                                <a class="button is-success btn-block" href="{{ route('fleet.provider.approve', $provider->id ) }}">Aprove</a>
                                @endif
                                <button type="button"
                                    class="button is-info btn-block dropdown-toggle"
                                    data-toggle="dropdown">@lang('admin.action')
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('fleet.provider.request', $provider->id) }}" class="button is-default"><i class="fa fa-search"></i> @lang('admin.History')</a>
                                    </li>
                                    @if( Setting::get('demo_mode', 0) == 0)
                                        <li>
                                            <a href="{{ route('fleet.provider.edit', $provider->id) }}" class="button is-default"><i class="fa fa-pencil"></i>  @lang('admin.edit')</a>
                                        </li>

                                    <li>
                                        <form action="{{ route('fleet.provider.destroy', $provider->id) }}" method="POST">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button class="button is-default look-a-like" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i> @lang('admin.delete')</button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>@lang('admin.id')</th>
                        <th>@lang('admin.provides.full_name')</th>
                        <th>@lang('admin.email')</th>
                        <th>@lang('admin.mobile')</th>
                        <th>@lang('admin.users.Wallet_Amount')</th>
                        <th>@lang('admin.provides.total_requests')</th>
                        <th>@lang('admin.provides.accepted_requests')</th>
                        <th>@lang('admin.provides.service_type')</th>
                        <th>@lang('admin.provides.online')</th>
                        <th>@lang('admin.action')</th>
                    </tr>
                </tfoot>
            </table>
                @include('common.pagination')
        </div>
    </div>
    </div>
</div>
@endsection
