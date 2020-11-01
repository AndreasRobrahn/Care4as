<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\DataImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

use App\DailyAgent;
use App\CapacitySuitReport;


class ExcelEditorController extends Controller
{
    public function test(Request $request)
    {
      $request->validate([
        'file' => 'required',
        // 'name' => 'required',
      ]);
      $file = request()->file('file');
      $path = $file->getRealPath();
      $data = Excel::ToArray(new DataImport, request()->file('file'));

      for($i=0;$i <= 10; $i++ )
      {
        unset($data[0][$i]);
      }
      // $keyarray = array(0 => "xy", 1 => "xy", 2 => "xy", 3 => "xy", 4 => "xy", 5 => "xy", 6 => "xy", 7 => "xy", 8 => "xy", 9 => "xy", 10 => "xy");
      // $convArray = array_diff_key($data[0], $keyarray);

      // array_splice($data[0],-7);
      // dd($data[0]);
        foreach($data[0] as $cell)
        {
          $dailyAgent = new DailyAgent;

          if($cell[4] == '')
          {
            $cell[4] = 0;
          }
          if($cell[13] == '')
          {
            $cell[13] = 0;
          }
          if($cell[15] == '')
          {
            $cell[15] = 0;
          }
          //convert all the excel dates to unix date
          $UNIX_DATE = ($cell[0] - 25569) * 86400;
          $dailyAgent->date = date("Y-m-d H:i:s", $UNIX_DATE);

          $UNIX_DATE2 = ($cell[23] - 25569) * 86400;
          $dailyAgent->start_time = date("Y-m-d H:i:s",$UNIX_DATE2);

          $UNIX_DATE3 = ($cell[24] - 25569) * 86400;
          $dailyAgent->end_time = date("Y-m-d H:i:s", $UNIX_DATE3);

          $dailyAgent->kw = $cell[2];
          $dailyAgent->dialog_call_id  = $cell[4];
          $dailyAgent->agent_id = $cell[6];
          $dailyAgent->agent_login_name = $cell[7];
          $dailyAgent->agent_name = $cell[8];
          $dailyAgent->agent_group_id = $cell[9];
          $dailyAgent->agent_group_name = $cell[10];
          $dailyAgent->agent_team_id = $cell[11];
          $dailyAgent->agent_team_name = $cell[14];
          $dailyAgent->queue_id = $cell[20];
          $dailyAgent->queue_name = $cell[21];
          $dailyAgent->skill_id = $cell[15];
          $dailyAgent->skill_name = $cell[16];
          $dailyAgent->status = $cell[22];
          $dailyAgent->time_in_state = $cell[25];
          $dailyAgent->timezone = $cell[26];

          $dailyAgent->save();

        }
      // $array = (new ExcelImport)->toArray($file);
      return redirect()->back();
    }
    public function capacitysuitReport ()
    {
      return view('reports.capacityReport');
    }
    public function capacitysuitReportUpload(Request $request)
    {
      // the capacitysuitReport variable stores one entry for the capacityReport table
      $capaySuitInput = null;

      $request->validate([
        'file' => 'required',
        // 'name' => 'required',
      ]);

      $file = request()->file('file');
      $path = $file->getRealPath();
      $data = Excel::ToArray(new DataImport, request()->file('file'));

      // for($i=0;$i <= 10; $i++ )
      // {
      //   unset($data[0][$i]);
      // }
      $transData = $data[0];
      // dd($transData = $data[0]);
      // foreach($transData as $array)
      // {
      //     $capaySuitInput->$transData[0] = $array[0];
      //     $capaySuitInput->$transData[1] = $array[1];
      //     $capaySuitInput->$transData[2] = $array[2];
      // }

      // return  count($transData[0]);
      for($i = 0; $i< (count($transData))-1; $i++)
      {
        $capacySuitInput = new CapacitySuitReport;
        for($j=0; $j < (count($transData[0])-1); $j++)
        {
          $property = $transData[0][$j];
          if(!$property == '')
          $capacySuitInput->$property = $transData[$i+1][$j];
          $property= null;
        }

        if(!CapacitySuitReport::where('target_date',$capacySuitInput->target_date)->where('agent_id',$capacySuitInput->agent_id)->exists())
        {
          $capacySuitInput->save();
        }

      }
      return redirect()->back();
    }

}
