<?php

namespace App\Http\Controllers\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Tickets\File;
use App\Models\Tickets\Ticket;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public static function upload(array $files, int $ticketId, int $replyId = null)
    {
        if (is_null($replyId)) {
            $objectId = $ticketId;
            $path = sprintf('tickets/%s', $ticketId);
            $itemsArray = [
                'ticket_id' => $objectId,
                'reply_id' => null,
            ];
        } else {
            $path = sprintf('tickets/%s/replies/%s', $ticketId, $replyId);
            $itemsArray = [
                'ticket_id' => $ticketId,
                'reply_id' => $replyId,
            ];
        }
        if (!is_null($files) && is_array($files)) {
            foreach ($files as $file) {
                $fileName = static::generateFileName($file);
                $file->storeAs($path, $fileName, 'public');
                $attributes = array_merge($itemsArray, [
                    'name' => $fileName,
                    'size' => $file->getSize()
                ]);
                File::create($attributes);
            }
        }
    }

    public static function generateFileName($file)
    {
        $ext = $file->getClientOriginalExtension();
        $fileName = str_replace(' ', '_', str_replace('.', '_', $file->getClientOriginalName())) . '_' . rand(1111, 9999) . '.' . $ext;
        $existence = File::where('name', $fileName)->exists();
        if ($existence) return self::generateFileName($file);
        return $fileName;
    }
}
