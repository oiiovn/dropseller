<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CloudinaryController extends Controller
{
    // Hàm upload ảnh lên Cloudinary
    public function uploadImage(Request $request)
    {
        // Kiểm tra nếu có file được gửi lên
        if ($request->hasFile('image')) {
            $uploadedFile = $request->file('image'); // Lấy file từ request

            // Upload ảnh lên Cloudinary
            $uploadedImage = Cloudinary::upload($uploadedFile->getRealPath(), [
                'folder' => 'uploads' // Thư mục trong Cloudinary
            ]);

            // Lấy URL của ảnh sau khi upload
            $imageUrl = $uploadedImage->getSecurePath();

            return response()->json([
                'message' => 'Upload thành công!',
                'url' => $imageUrl
            ]);
        }

        return response()->json([
            'message' => 'Không có file nào được tải lên.'
        ], 400);
    }

    // Hàm xóa ảnh trên Cloudinary
    public function deleteImage(Request $request)
    {
        $publicId = $request->input('public_id'); // Lấy public_id từ request

        if ($publicId) {
            Cloudinary::destroy($publicId); // Xóa ảnh bằng public_id
            return response()->json([
                'message' => 'Xóa ảnh thành công!'
            ]);
        }

        return response()->json([
            'message' => 'Không tìm thấy public_id.'
        ], 400);
    }
}
