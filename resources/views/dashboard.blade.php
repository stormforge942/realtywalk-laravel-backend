@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="animated fadeIn">

    {{-- Data Summary --}}
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            {{ __('dashboard.summary.title') }}
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-6 col-sm-3">
                <div class="c-callout c-callout-info">
                  <small class="text-muted">
                    {{ __('dashboard.summary.properties') }}
                  </small>
                  <div class="text-value-lg">
                    <countup-text :end-val="{{ $total['properties'] }}" />
                  </div>
                </div>
              </div>

              <div class="col-6 col-sm-3">
                <div class="c-callout c-callout-danger">
                  <small class="text-muted">
                    {{ __('dashboard.summary.builders') }}
                  </small>
                  <div class="text-value-lg">
                    <countup-text :end-val="{{ $total['builders'] }}" />
                  </div>
                </div>
              </div>

              <div class="col-6 col-sm-3">
                <div class="c-callout c-callout-warning">
                  <small class="text-muted">
                    {{ __('dashboard.summary.polygons') }}
                  </small>
                  <div class="text-value-lg">
                    <countup-text :end-val="{{ $total['polygons'] }}" />
                  </div>
                </div>
              </div>

              <div class="col-6 col-sm-3">
                <div class="c-callout c-callout-success">
                  <small class="text-muted">
                    {{ __('dashboard.summary.users') }}
                  </small>
                  <div class="text-value-lg">
                    <countup-text :end-val="{{ $total['users']['total'] }}"></countup-text>

                    (<countup-text :end-val="{{ $total['users']['agent'] }}"></countup-text>&#47;<countup-text :end-val="{{ $total['users']['regular'] }}"></countup-text>)
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Properties Data --}}
    <div class="row">
      <div class="col-sm-6">
        <div class="card">
          <div class="card-header">
            {{ __('dashboard.total_properties_per_category') }}
          </div>
          <div class="card-body">
            <chart-pie fetch-url="{{ route('dashboard.properties_per_category') }}"></chart-pie>
          </div>
        </div>
      </div>

      <div class="col-sm-6">
        <div class="card">
          <div class="card-header">
            {{ __('dashboard.total_properties_per_style') }}
          </div>
          <div class="card-body">
            <chart-doughnut fetch-url="{{ route('dashboard.properties_per_style') }}"></chart-doughnut>
          </div>
        </div>
      </div>
    </div>

    {{-- Latest Registered Users --}}
    @if($users->count())
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            {{ __('dashboard.last_registered_users.title') }}
          </div>
          <div class="card-body p-0">
            <table class="table table-responsive-sm table-hover table-outline mb-0">
              <thead class="thead-light">
                <tr>
                  <th class="text-center text-nowrap" style="width: 1%;">
                    <i class="cil-people"></i>
                  </th>
                  <th>
                    {{ __('dashboard.last_registered_users.table.columns.user_name') }}
                  </th>
                  <th>
                    {{ __('dashboard.last_registered_users.table.columns.activity') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                @foreach($users as $user)
                <tr>
                  <td class="text-center">
                    <div class="c-avatar">
                      <img class="c-avatar-img" src="{{ $user->picture_path ?: \Avatar::create($user->name)->toBase64() }}" alt="{{ $user->email }}" />
                    </div>
                  </td>
                  <td>
                    <div>{{ $user->name }}</div>
                    <div class="small text-muted">
                      {{ __('dashboard.last_registered_users.table.rows.registered_at', ['datetime' => $user->created_at->toDayDateTimeString()]) }}
                    </div>
                  </td>
                  <td>
                    <div class="small text-muted">
                      {{ __('dashboard.last_registered_users.table.rows.last_login_at') }}
                    </div>
                    <strong>{{ $user->last_login_at !== null ? $user->last_login_at->diffForHumans() : '-' }}</strong>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>
@endsection
