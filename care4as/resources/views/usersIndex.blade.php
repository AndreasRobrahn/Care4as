@extends('general_layout')

@section('content')
<div class="container-fluid" style="width: 75vw;">
  <div class="row ">
    <div class="col text-center text-white bg-light" style="border-radius: 15px;">
      <div class="card card-nav-tabs card-plain">
      <div class="card-header card-header-danger">
          <!-- colors: "header-primary", "header-info", "header-success", "header-warning", "header-danger" -->
          <div class="nav-tabs-navigation">
              <div class="nav-tabs-wrapper">
                  <ul class="nav nav-tabs" data-tabs="tabs">
                      <li class="nav-item">
                          <a class="nav-link active" href="#userinfo" data-toggle="tab">Userdaten</a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link" href="#userstats" data-toggle="tab">Userstatistiken</a>
                      </li>
                      <li class="nav-item">
                          <a class="nav-link" href="#history" data-toggle="tab">Weitere Funktion</a>
                      </li>
                  </ul>
              </div>
          </div>
      </div><div class="card-body ">
          <div class="tab-content text-center">
              <div class="tab-pane active" id="userinfo">
                <table class="table table-striped">
                <thead>
                  <tr>
                  <th>#</th>
                    <th>name</th>
                    <th>options</th>
                  <tr>
                </thead>
                <tbody>
                  @foreach($users as $user)
                    <tr>
                      <td>{{$user->id}}</td>
                      <td>{{$user->name}}</td>

                      <td>
                        <a href="">
                        <button class="btn btn-danger btn-fab btn-icon btn-round">
                            <i class="now-ui-icons ui-1_simple-remove"></i>
                        </button>
                        </a>
                        <a href="{{route('user.show', ['id' => $user->id])}}">
                        <button class="btn btn-success btn-fab btn-icon btn-round">
                            <i class="now-ui-icons files_single-copy-04"></i>
                        </button>
                        </a>
                        <a href="">
                        <button class="btn btn-primary btn-fab btn-icon btn-round">
                          <i class="now-ui-icons ui-1_settings-gear-63"></i>
                        </button>
                        </a>
                      </td>
                    <tr>
                  @endforeach
                </tbody>
            </table>
              </div>
              <div class="tab-pane fade" id="userstats">
                <div class="row text-dark">
                  <table class="table table-striped">
                  <thead>
                    <tr>
                    <th>#</th>
                      <th>name</th>
                      <th>Calls</th>
                      <th>Saves SSC</th>
                      <th>Cancels SSC</th>
                      <th>Service SSC</th>
                      <th>Saves BSC</th>
                      <th>Cancels BSC</th>
                      <th>Service BSC</th>
                      <th>Saves Portal</th>
                      <th>Cancels Portal</th>
                      <th>Service Portal</th>
                      <th>Optionen</th>
                    <tr>
                  </thead>
                  <tbody>
                    @foreach($users as $user)
                      <tr>
                        <td>{{$user->id}}</td>
                        <td>{{$user->name}}</td>
                        <td>@if($user->TrackingToday){{$user->TrackingToday->sum('calls')}} @else 0 @endif</td>
                        <td>@if($user->TrackingToday){{$user->TrackingToday->where('type','SSC')->where('division','Retention')->sum('save')}} @else 0 @endif</td>
                        <td>@if($user->TrackingToday){{$user->TrackingToday->where('type','SSC')->where('division','Retention')->sum('cancel')}} @else 0 @endif</td>
                        <td>@if($user->TrackingToday){{$user->TrackingToday->where('type','SSC')->where('division','Retention')->sum('service')}} @else 0 @endif</td>
                        <td>@if($user->TrackingToday){{$user->TrackingToday->where('type','BSC')->where('division','Retention')->sum('save')}} @else 0 @endif</td>
                        <td>@if($user->TrackingToday){{$user->TrackingToday->where('type','BSC')->where('division','Retention')->sum('cancel')}} @else 0 @endif</td>
                        <td>@if($user->TrackingToday){{$user->TrackingToday->where('type','BSC')->where('division','Retention')->sum('service')}} @else 0 @endif</td>
                        <td>@if($user->TrackingToday){{$user->TrackingToday->where('type','Portal')->where('division','Retention')->sum('save')}} @else 0 @endif</td>
                        <td>@if($user->TrackingToday){{$user->TrackingToday->where('type','Portal')->where('division','Retention')->sum('cancel')}} @else 0 @endif</td>
                        <td>@if($user->TrackingToday){{$user->TrackingToday->where('type','Portal')->where('division','Retention')->sum('service')}} @else 0 @endif</td>
                        <td>
                          <a href="">

                          </a>

                        </td>
                      <tr>
                    @endforeach
                  </tbody>
              </table>
                </div>

              </div>
              <div class="tab-pane" id="history">

              </div>
          </div>
      </div>
    </div>

    </div>

  </div>
</div>

@endsection