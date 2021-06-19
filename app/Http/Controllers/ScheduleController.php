<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Day;
use App\Models\Schedule;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function create(Request $request)
    {
        try {

            $actor = $request->user();

            if (!$actor->role == 'admin') {
                throw new \Exception('maaf kamu tidak bisa mengakses halaman ini!');
            }

            $request->validate([
                'day_id' => 'required',
                'halaqah_id' => 'required',
                'time_start' => 'required',
                'time_end' => 'required',
                'session' => ['required', 'in:morning, soon, night'],
            ]);

            $schedule = new Schedule([
                'day_id' => $request->day_id,
                'halaqah_id' => $request->halaqah_id,
                'time_start' => $request->time_start,
                'time_end' => $request->time_end,
                'session' => $request->session,

            ]);

            $schedule->save();

            return ResponseFormatter::success($schedule);
        } catch (Exception $err) {
            return ResponseFormatter::error($err->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $actor = $request->user();

            if (!$actor->hasRole('admin')) {
                throw new \Exception('maaf kamu tidak bisa mengakses halaman ini!');
            }

            $schedule = Schedule::findOrFail($id);

            if (!$schedule) {
                throw new \Exception('maaf schedule yang anda tuju tidak tersedia');
            }

            $schedule->update($request->all());

            return ResponseFormatter::success($schedule);
        } catch (Exception $err) {
            return ResponseFormatter::error($err->getMessage());
        }
    }
    public function delete(Request $request, $id)
    {
        try {
            $actor = $request->user();

            if (!$actor->hasRole('admin')) {
                throw new \Exception('maaf kamu tidak bisa mengakses halaman ini!');
            }

            $schedule = Schedule::findOrFail($id);

            if (!$schedule) {
                throw new \Exception('maaf schedule yang anda tuju tidak tersedia');
            }

            $schedule->delete();
        } catch (Exception $err) {
            return ResponseFormatter::error($err->getMessage());
        }
    }
    public function get(Request $request)
    {
        $id = $request->input('id');
        $day_id = $request->input('day_id');
        $halaqah_id = $request->input('halaqah_id');

        try {
            if ($id) {
                $schedule = Schedule::find($id);
                return ResponseFormatter::success($schedule);
            }

            $schedules = Schedule::query();

            if ($day_id) {
                $schedules->where('day_id', $day_id)->get();
            } else if ($halaqah_id) {
                $schedules->where('halaqah_id', $halaqah_id)->get();
            } else {
                $schedules->all();
            }

            return ResponseFormatter::success($schedules);
        } catch (Exception $err) {
            return ResponseFormatter::error($err->getMessage());
        }
    }
}
