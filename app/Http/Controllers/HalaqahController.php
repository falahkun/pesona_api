<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Halaqah;
use Exception;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class HalaqahController extends Controller
{
    public function create(Request $request)
    {
        try {

            $actor = $request->user();

            if ($actor->role != 'admin') {
                throw new \Exception('maaf kamu tidak bisa mengakses halaman ini!');
            }

            $request->validate([
                'name' => ['required', 'max:255'],
                'time_start' => ['required'],
                'time_end' => ['required'],
                'asatidz_id' => 'required',
            ]);

            $halaqah = new Halaqah([
                'name' => $request->name,
                'time_start' => $request->time_start,
                'time_end' => $request->time_end,
                'asatidz_id' => $request->asatidz_id,
            ]);

            $halaqah->save();

            return ResponseFormatter::success($halaqah);
        } catch (Exception $err) {
            return ResponseFormatter::error($err->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $actor = $request->user();

            if (!$actor->role == 'admin') {
                throw new \Exception('maaf kamu tidak bisa mengakses halaman ini!');
            }

            $halaqah = Halaqah::findOrFail($id);

            if (!$halaqah) {
                throw new \Exception('maaf halaqah yang anda tuju tidak tersedia');
            }

            $halaqah->update($request->all());

            return ResponseFormatter::success($halaqah);
        } catch (Exception $err) {
            return ResponseFormatter::error($err->getMessage());
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $actor = $request->user();

            if (!$actor->role == 'admin') {
                throw new \Exception('maaf kamu tidak bisa mengakses halaman ini!');
            }

            $halaqah = Halaqah::findOrFail($id);

            if (!$halaqah) {
                throw new \Exception('maaf halaqah yang anda tuju tidak tersedia');
            }

            $halaqah->delete();

            return ResponseFormatter::success('success halaqah deleted');
        } catch (Exception $err) {
            return ResponseFormatter::error($err->getMessage());
        }
    }

    public function get(Request $request)
    {
        $id = $request->input('id');
        $asatidz = $request->input('asatidz');
        $timeStart = $request->input('timeStart');
        $timeEnd = $request->input('timeEnd');

        try {

            if ($id) {
                $halaqah = Halaqah::find($id);
                return ResponseFormatter::success($halaqah);
            }

            $halaqah = Halaqah::query();

            if ($asatidz) {
                $halaqah->asatidz()->where('name', $asatidz)->get();
            } else {
                $halaqah->get();
            }

            return ResponseFormatter::success($halaqah->paginate(10));
        } catch (Exception $err) {
            return ResponseFormatter::error($err->getMessage());
        }
    }
}
