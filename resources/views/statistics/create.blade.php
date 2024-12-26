@extends('layouts.app')

@section('title')
  {!! __('statistic.create.meta.title') !!}
@endsection

@section('content')
	<div class="container-fluid">
		<div class="animated fadeIn">
			@include('coreui-templates::common.errors')
			<ul class="errorlist"></ul>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-header">
							<i class="fa fa-plus-square-o fa-lg"></i>
							<strong>
								{{ __('statistic.create.title') }}
							</strong>
						</div>
						<div class="card-body">
							{!! Form::open(
                [
                  'route' => 'statistics.store',
                  'class' => 'form-ajax',
                  'data-msg' => __('statistic.create.saved_message'),
                  'data-to' => '/system/polygons/statistics'
                ]
              ) !!}

								@include('statistics.fields')

							{!! Form::close() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
