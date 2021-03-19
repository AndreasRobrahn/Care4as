<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Hoursreport;
use App\RetentionDetail;
use App\DailyAgent;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function presentation(Request $request)
    {
      DB::disableQueryLog();
      ini_set('memory_limit', '-1');
      ini_set('max_execution_time', '0'); // for infinite time of execution

      $modul = 'Userübersicht';

      $start_date = 1;
      $end_date = 1;

      if($request->start_date)
      {
        $start_date = $request->start_date;
      }
      if ($request->end_date) {
        $end_date = $request->end_date;
      }
      // return $end_date;

      if($request->employees)
      {
        $users = User::where('role','agent')
        ->whereIn('id', $request->employees)
        ->select('id','surname','lastname','person_id','agent_id','dailyhours','department')
        ->with(['dailyagent' => function($q) use ($start_date,$end_date){
          $q->select(['id','agent_id','status','time_in_state','date']);

          if($start_date != 1)
          {
            $datemod = \Carbon\Carbon::parse($start_date)->setTime(2,0,0);
            // return $datemod ;
            $q->where('date','>=',$datemod);
          }
          if($end_date != 1)
          {
            $datemod2 = \Carbon\Carbon::parse($end_date)->setTime(23,59,59);
            // return $end_date ;
            $q->where('date','<=',$datemod2);
          }
          }])
          ->with(['retentionDetails' => function($q) use ($start_date,$end_date){
          // $q->select(['id','person_id','calls','time_in_state','call_date']);
          if($start_date !== 1)
          {
            $q->where('call_date','>=',$start_date);
          }
          if($end_date !== 1)
          {
            $q->where('call_date','<=',$end_date);
          }
          }])
        // ->limit(10)
        ->get();
      }
      else {

        if ($request->department) {
          $department = $request->department;
        }
        else {
          $department = '';
        }
        // return \Carbon\Carbon::parse($start_date)->setTime(2,0,0);
        $users = User::where('role','agent')
        ->where('department', $department)
        ->select('id','surname','lastname','person_id','agent_id','dailyhours','department')
        ->where('agent_id','!=',null)
        ->with(['dailyagent' => function($q) use ($start_date,$end_date){
          $q->select(['id','agent_id','status','time_in_state','date']);
          if($start_date !== 1)
          {
            $datemod = \Carbon\Carbon::parse($start_date)->setTime(2,0,0);
            $q->where('date','>=',$datemod);
          }
          if($end_date !== 1)
          {
            $datemod2 = \Carbon\Carbon::parse($end_date)->setTime(23,59,59);
            $q->where('date','<=',$datemod2);
          }
          }])
        ->with(['retentionDetails' => function($q) use ($start_date,$end_date){
          // $q->select(['id','person_id','calls','time_in_state','call_date']);
          if($start_date !== 1)
          {
            $q->where('call_date','>=',$start_date);
          }
          if($end_date !== 1)
          {
            $q->where('call_date','<=',$end_date);
          }
          }])
        // ->limit(10)
        ->get();
      }


      foreach ($users as $key => $user) {

        $reports = $user->retentionDetails;
        $sumorders = $reports->sum('orders');
        // sum of all calls during the timespan
        $sumcalls = $reports->sum('calls');
        $sumNMlz = $reports->sum('mvlzNeu');
        $sumrlz24 = $reports->sum('rlzPlus');
        $sumSSCCalls = $reports->sum('calls_smallscreen');
        $sumBSCCalls = $reports->sum('calls_bigscreen');
        $sumPortalCalls = $reports->sum('calls_portale');
        $sumSSCOrders = $reports->sum('orders_smallscreen');
        $sumBSCOrders = $reports->sum('orders_bigscreen');
        $sumPortalOrders = $reports->sum('orders_portale');

        $ahtStates = array('On Hold','Wrap Up','In Call');

        $casetime = $user->dailyagent->whereIn('status', $ahtStates)->sum('time_in_state');

        $calls = $user->dailyagent->where('status', 'Ringing')
        ->count();

        if($calls == 0)
        {
          $AHT = 0;
        }
        else {
          $AHT =  round(($casetime/ $calls),0);
        }

        $workdays = $reports->count();
        $workedHours = 0;
        $sickHours = 0;
        $sicknessquota = '';

        if($sumSSCCalls == 0)
        {
          $SSCQouta = 0;
        }
        else {
          $SSCQouta = round(($sumSSCOrders/$sumSSCCalls)*100,2).'%';
        }
        if($sumBSCCalls == 0)
        {
          $BSCQuota = 0;
        }
        else {
          $BSCQuota = round(($sumBSCOrders/$sumBSCCalls)*100,2).'%';
        }
        if($sumPortalCalls == 0)
        {
          $portalQuota = 0;
        }
        else {
          $portalQuota = round(($sumPortalOrders/$sumPortalCalls) *100,2).'%';
        }

        if($sumrlz24 == 0 or $sumNMlz == 0)
        {
          $RLZQouta = 'keine Daten';
        }
        else {
          $RLZQouta = round((($sumrlz24 / ($sumrlz24 + $sumNMlz))*100),2).'%';
        }
        if($sumcalls == 0)
        {
          $gevocr = 'Fehler: keine Calls';
        }
        else
        {
          $gevocr = round(($sumorders/$sumcalls) * 100,2).'%';
        }

        $queryWorktime = Hoursreport::query();
        $queryWorktime->where('user_id',$user->id)
        ->orderBY('date','DESC');
        // the filter section
        $queryWorktime->when(request('start_date'), function ($q) {
            return $q->where('date', '>=',request('start_date'));
        });
        $queryWorktime->when(request('end_date'), function ($q) {
            return $q->where('date', '<=',request('end_date'));
        });

        $workTimeData = $queryWorktime->get();
        // dd($user->workTimeData);
        $workedHours = $workTimeData->sum('IST_Angepasst');
        $sickHours = $workTimeData->sum('sick_Angepasst');

        if($workedHours == 0)
        {
          $sicknessquota = 'keine validen Daten';
        }
        else {
          $sicknessquota =  ($sickHours/$workedHours)*100;
        }

        $payed = round(($user->dailyagent->sum('time_in_state')/3600),2);

        $productiveStates = array('Wrap Up','Ringing', 'In Call','On Hold','Available','Released (05_occupied)','Released (06_practice)','Released (09_outbound)');

        $productive = $user->dailyagent->whereIn('status', $productiveStates)
        ->sum('time_in_state');

        $productive = round(($productive/3600),2);

        $user->salesdata = array(
          'calls' => $sumcalls,
          'orders' => $sumorders,
          'workedDays' => $workdays,
          'sscQuota' => $SSCQouta,
          'sscOrders' => $sumSSCOrders,
          'bscQuota' =>  $BSCQuota,
          'bscOrders' => $sumBSCOrders,
          'portalQuota' => $portalQuota,
          'portalOrders' => $sumPortalOrders,
          'RLZ24Qouta' => $RLZQouta,
          'GeVo-Cr' => $gevocr,
          'workedHours' => $workedHours,
          'sickHours' => $sickHours,
          'payedtime' => $payed,
          'productive' => $productive,
          'aht' => $AHT,
        );
      }
      // return view('usersIndex', compact('users'));
      return view('presentation', compact('modul', 'users'));
    }
    public function FunctionName($value='')
    {

    }
}
