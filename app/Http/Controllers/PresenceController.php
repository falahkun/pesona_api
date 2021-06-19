<?php

namespace App\Http\Controllers;

use App\Helpers\FirebaseCloudMessaging;
use App\Helpers\ResponseFormatter;
use App\Models\Presence;
use App\Models\PresenceDetail;
use App\Models\User;
use App\Models\Schedule;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class PresenceController extends Controller
{
    /// create on cronjob
    public function createPresence(Request $request)
    {

        $now = Carbon::now();

        $dayOfTheWeek = $now->dayOfWeek;


        $schedules = Schedule::where('day_id', $dayOfTheWeek + 1)->get();

        $listOfTimes = array();

        foreach ($schedules as $schedule) {
            /// initialize presence


            /// get in one hours before started event
            $explodedTimeStart = explode(':', $schedule->time_start);
            /// get in one hours before started event
            $explodedTimeEnd = explode(':', $schedule->time_start);
            $hoursStart = $explodedTimeStart[0];
            $hoursEnd = $explodedTimeEnd[0];

            // $hoursStart = (int) $hoursStart - 1;


            $now = Carbon::now();
            
            dd($schedule);
            
            

            // jika time start sama dengan time server

            if ($now->format('h:i') == $schedule->time_start) {
                $presence = new Presence();
                $presence->id = Uuid::uuid6()->toString();
                $presence->date = Carbon::now();
                $presence->schedule_id = $schedule->id;
                $presence->save();
                
                dd($presence);
                FirebaseCloudMessaging::pushNotification(array(), 'global');
            }


            // jika time end sama dengan time server

            if ($now->format('h:i') == $hoursEnd . ':00') {
                $presence = Presence::where('schedule_id', $schedule->id)->where('is_active', true)->get()->first();
                $presence->is_active = false;
                $presence->update();

                FirebaseCloudMessaging::pushNotification(array(), 'global');
            }
        }

        // $schedule


    }
    
    public function create(Request $request) {
        try {
            $actor = $request->user();

            if (!$actor->role == 'admin') {
                throw new \Exception('maaf kamu tidak bisa mengakses halaman ini!');
            }
            
            
            $request->validate([
                'schedule_id' => 'required',
                ]);
                
                $presence = new Presence();
                $presence->id = Uuid::uuid6()->toString();
                $presence->date = Carbon::now();
                $presence->schedule_id = $request->schedule_id;
                $presence->save();
                
                // FirebaseCloudMessaging::pushNotification(array(), 'global');
            
            return ResponseFormatter::success($presence);
        } catch (Exception $err) {
            return ResponseFormatter::error($err->getMessage());
        }
    }

    public function absent(Request $request)
    {
        try {
            $request->validate([
                'presence_id' => ['required'],
                'present_type' => ['nullable', 'in:absent,a'],
                'absent_message' => ['nullable'],
                'latitude' => ['nullable'],
                'longitude' => ['nullable'],
            ]);

            $presence = Presence::find($request->presence_id);
            
            if ($request->present_type != null) {
                if ($request->present_type != 'absent') {
                
                    throw new \Exception('format `present_type` salah, format yang dibolehkan `absent, present`');
                } else if ($request->present_type != 'present') {
                    
                    throw new \Exception('format `present_type` salah, format yang dibolehkan `absent, present`');
                }
            }

            if (!$presence) {
                throw new \Exception('maaf, sesi sudah tidak ditemukan. coba hubungi admin!');
            }

            if (!$presence->is_active) {
                throw new \Exception('maaf, sesi sudah berakhir, lain kali jangan telat ya!');
            }
            
            if ($request->user()->role != 'user') {
                throw new \Exception('maaf, selain user tidak diperkenankan untuk mengakses halaman ini');
            }
            
            if (!$request->user()->active) {
                throw new \Exception('akun anda sedang dalam tahap verifikasi, mohon kesediaannya untuk menunggu');
            }

            $presenceDetail = new PresenceDetail();
            $presenceDetail->presence_id = $request->presence_id;
            $presenceDetail->user_id = $request->user()->id;
            $presenceDetail->presence_type = $request->presence_type ?? 'present';
            $presenceDetail->absent_message = $request->absent_message;
            $presenceDetail->latitude = $request->latitude;
            $presenceDetail->longitude = $request->longitude;

            $presenceDetail->save();

            return ResponseFormatter::success($presenceDetail);
        } catch (ValidationException $err) {
            throw new \Exception($err->getMessage());
        } catch (Exception $err) {
            return ResponseFormatter::error($err->getMessage());
        }
    }

    public function getPresence(Request $request)
    {
        try {
            $id = $request->input('id');
            $schedule = $request->input('schedule_id');
            $active = $request->input('active');
            $userId = $request->user()->id;

            if ($id) {
                $presence = Presence::findOrFail($id);
                $presence['you'] = PresenceDetail::where('presence_id', $id)->where('user_id', $userId)->first() ? 'present' : 'not_yet_present';
                
                if ($request->user()->role == 'admin') {
                    $users = User::where('active', true)->where('role', 'user')->count();
                    
                    $presenceDetail = PresenceDetail::where('presence_id', $id)->count(); 
                    
                    $presence['not_yet_present'] = (int)$users - (int)$presenceDetail;
                }
                return ResponseFormatter::success($presence);
            }

            $presences = Presence::query()->with(['users']);

            if ($schedule) {
                $presences->where('schedule_id', $schedule);
            }

            if ($active) {
                $presences->where('is_active', $active);
            }

            return ResponseFormatter::success($presences->paginate(10));
        } catch (Exception $err) {
            return ResponseFormatter::error($err->getMessage());
        }
    }
}
