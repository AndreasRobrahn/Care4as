@extends('general_layout')

@section('additional_css')

<style media="screen">

  th, td
  {
    font-size: 0.7vw !important;
    text-align: left;
    overflow: hidden;
    /* text-overflow: ellipsis; */
  }
  .loader {
  font-size: 2px;
  /* margin: 50px auto; */
  text-indent: -9999em;
  width: 11em;
  height: 11em;
  border-radius: 50%;
  /* background: #ffffff; */
  /* background: -moz-linear-gradient(left, #ffffff 10%, rgba(255, 255, 255, 0) 42%); */
  /* background: -webkit-linear-gradient(left, #ffffff 10%, rgba(255, 255, 255, 0) 42%); */
  /* background: -o-linear-gradient(left, #ffffff 10%, rgba(255, 255, 255, 0) 42%); */
  /* background: -ms-linear-gradient(left, #ffffff 10%, rgba(255, 255, 255, 0) 42%); */
  /* background: linear-gradient(to right, #ffffff 10%, rgba(255, 255, 255, 0) 42%); */
  position: relative;
  -webkit-animation: load3 1.4s infinite linear;
  animation: load3 1.4s infinite linear;
  -webkit-transform: translateZ(0);
  -ms-transform: translateZ(0);
  transform: translateZ(0);
}
.loader:before {
  width: 50%;
  height: 50%;
  background: #0dc5c1;
  border-radius: 100% 0 0 0;
  position: absolute;
  top: 0;
  left: 0;
  content: '';
}
.loader:after {
  background: #ffffff;
  width: 75%;
  height: 75%;
  border-radius: 50%;
  content: '';
  margin: auto;
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
}
@-webkit-keyframes load3 {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes load3 {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
</style>

@endsection

@section('content')

<div class="container-fluid bg-light m-1">
  <div class="row justify-content-center align-self-center m-1">
      <h4 >Präsentation des aktuellen Moduls: {{$modul ?? ''}}</h4>
  </div>

  <div class="row bg-white shadow  m-1 mt-4" id="filtermenu">
    <div class="col-12 d-flex justify-content-center align-self-center">
      <h5>Filtermenü</h5>
    </div>
    <div class="col-12">
      <form class="mt-2 w-100" action="{{route('presentation')}}" method="get">
        <div class="row m-0 justify-content-center">
          <div class="col-6 p-0" style="border-right: 2px solid black;">
            <div class="row m-2 justify-content-end">
              <div class="col-4 ml-1 p-0">
                <label for="department">Abteilung:</label>
                <select class="form-control" name="department" id="department" style="width:218px;">
                  <option value="" @if(!request('department')) selected @endif disabled>Wähle die Abteilung</option>
                  <option value="1&1 DSL Retention" @if(request('department') == '1&1 DSL Retention') selected @endif>1&1 DSL Retention</option>
                  <option value="1&1 Mobile Retention" @if(request('department') == '1&1 Mobile Retention') selected @endif>1&1 Mobile Retention</option>
                </select>
              </div>
              <div class="col-3 p-0 mr-2">
                <label for="department">Welche MA:</label>
                <select multiple class="form-control" name="employees[]" id="exampleFormControlSelect2" style="height: 50px; overflow:scroll;">
                  @if(request('department'))
                    @foreach($users1 = App\User::where('department',request('department'))->where('role','agent')->get() as $user)
                      <option value="{{$user->id}}">{{$user->surname}} {{$user->lastname}}</option>
                    @endforeach
                  @else
                    @foreach($users1 = App\User::where('role','agent')->where('department','1&1 Mobile Retention')->get() as $user)
                      <option value="{{$user->id}}">{{$user->surname}} {{$user->lastname}}</option>
                    @endforeach
                  @endif
                </select>
              </div>
            </div>
          </div>
          <div class="col-6 p-0">
            <div class="row m-2 justify-content-start">
              <div class="col-sm-3">
                <label for="datefrom">Von:</label>
                 <input type="date" id="start_date" name="start_date" class="form-control" placeholder="" value="{{request('start_date')}}">
               </div>
               <div class="col-sm-3">
                 <label for="dateTo">Bis:</label>
                 <input type="date" id="end_date" name="end_date" class="form-control" placeholder="" value="{{request('end_date')}}">
               </div>
            </div>
          </div>
        </div>
    </div>
    <div class="col-12 p-0">
      <div class="row m-2 justify-content-center">
        <button type="submit" name="button" class="btn-sm btn-success">Filter</button>
      </div>
    </div>
    </form>
  </div>
  <div class="row m-2 mt-4 bg-white shadow justify-content-center align-self-center" >
    <div class="col-12">
    <div class="col p-1">
      @php
        if(request('department') == '1&1 DSL Retention')
        {
          $pricepersave = 15;
        }
        else
        {
          $pricepersave = 15;
        }
      @endphp

      <table class="table table-hover table-striped table-bordered" id="tableoverview">
        <thead class="thead-dark">
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>AHT</th>
            <th>1&1 bezahlte Zeit</th>
            <th>1&1 Produktivzeit</th>
            <th>Saves</th>
            <th>Calls</th>
            <th>Calls/h</th>
            @if(request('department') == '1&1 Mobile Retention')
              <th>SSC</th>
              <th>BSC</th>
              <th>Portal</th>
            @endif
            <th>SSE</th>
            <th>Saves/h</th>
            <th>RLZ+24 %</th>
            <th>GO CR</th>
            <th>RET GeVo CR</th>
            <th>SSC CR</th>
            <th>BSC CR</th>
            <th>Portal CR</th>
            <th>KüRü CR</th>
            <th>Umsatz</th>
            <th>Umsatz/h bez</th>
            <th>Umsatz/h Std. prod</th>
            <th>KQ</th>
            <th>Optionen</th>
          </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
          <tr>
            <td>{{$user->id}}</td>
            <td>{{$user->surname}} {{$user->lastname}}</td>
            <!-- <td>{{$user->name}}</td> -->
            <td>
              {{$user->salesdata['aht']}}
            </td>
            <td>{{$user->salesdata['payedtime']}}</td>
            <td>{{$user->salesdata['productive']}}</td>
            @if($user->department == '1&1 DSL Retention')
              <td>{{$user->salesdata['orders']}}</td>
            @else
              <td>{{$sumSaves = $user->salesdata['sscOrders'] + $user->salesdata['bscOrders'] + $user->salesdata['portalOrders']}}</td>
            @endif
            <td>{{$user->salesdata['calls']}}</td>
            @if($user->dailyhours and $user->salesdata['workedDays'] != 0)
              <td>{{round($user->salesdata['calls'] / ($user->salesdata['workedDays']* $user->dailyhours),2)}}</td>
              @else
              <td>@if(!$user->dailyhours) Fehler Agent @else Fehler Retention Details @endif</td>
            @endif
            @if(request('department') == '1&1 Mobile Retention')
              <td>{{$user->salesdata['sscOrders']}}</td>
              <td>{{$user->salesdata['bscOrders']}}</td>
              <td>{{$user->salesdata['portalOrders']}}</td>
            @endif
            <td>50</td>
            @if($user->dailyhours and $user->salesdata['workedDays'] != 0)
              @if($user->department == '1&1 DSL Retention')
                <td>{{round($user->salesdata['orders']/($user->salesdata['workedDays'] * $user->dailyhours),2)}}</td>
              @else
                <td>{{round($sumSaves/($user->salesdata['workedDays'] * $user->dailyhours),2)}}</td>
              @endif
            @else
              <td>fehler</td>
            @endif
            <td>{{$user->salesdata['RLZ24Qouta']}}</td>
            <td>50</td>
            <td> {{$user->salesdata['GeVo-Cr']}}</td>
            <td>{{$user->salesdata['sscQuota']}}</td>
            <td>{{$user->salesdata['bscQuota']}}</td>
            <td>{{$user->salesdata['portalQuota']}}</td>
            <td>50</td>
            <td>{{$user->salesdata['orders'] * $pricepersave}}</td>
            @if($user->salesdata['payedtime'] != 0)
              <td>{{round(($user->salesdata['orders'] * $pricepersave)/($user->salesdata['payedtime']),2)}}€</td>
            @else
              <td>Fehler</td>
            @endif
            @if($user->salesdata['productive'] != 0)
              <td>{{round(($user->salesdata['orders'] * $pricepersave) / $user->salesdata['productive'],2) }}€</td>
            @else
                <td>Fehler: Arbeitsstunden Stundenreport</td>
            @endif
            <td>{{$user->salesdata['sicknessquota']}}%</td>
            <!-- <td>round($user->salesdata['sicknessquota'],2)%</td> -->
            <td>
              <a href="{{route('user.stats', ['id' => $user->id])}}">anzeigen</a>
            </td>
          </tr>
        @endforeach
      </tbody>
      <tfoot class="">
        <tr class="bg-dark text-white">
          <td>Total:</td>
          <td>1</td>
          <td>2</td>
          <td>3</td>
          <td>4</td>
          <td>5</td>
          <td>6</td>
          <td>7</td>
          <td>8</td>
          <td>9</td>
          <td>10</td>
          <td>11</td>
          <td>12</td>
          <td>13</td>
          <td>14</td>
          <td>15</td>
          <td>16</td>
          <td>17</td>
          <td>18</td>
          <td>19</td>
          <td id="revenue">20</td>
          <td>21</td>
          <td>22</td>
          <td>23</td>
          <td>24</td>
        </tr>
      </tfoot>
  </table>
    </div>
  </div>
</div>

@endsection

@section('additional_js')
<script src='https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js'></script>
<script src='https://cdn.datatables.net/plug-ins/1.10.24/api/sum().js'></script>

<script type="text/javascript">
  $(document).ready(function(){

    let table = $('#tableoverview').DataTable({
      ordering: true,
    });
    let element = $('#revenue')
    element.html('<b>'+table.column(20).data().sum()+'€ </b>')

  });
</script>
@endsection
