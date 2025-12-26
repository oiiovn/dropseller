<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class AssignUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:assign {email} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gán một vai trò cho người dùng theo email';

    /**
     * Command này dùng để:
     * - Gán nhanh vai trò cho người dùng qua CLI mà không cần vào giao diện admin
     * - Hữu ích khi cần:
     *   + Khởi tạo dữ liệu ban đầu (ví dụ: tạo tài khoản admin đầu tiên)
     *   + Tự động hóa quy trình gán quyền trong các scripts
     *   + Khắc phục sự cố khi bạn không thể truy cập giao diện admin
     * 
     * Cách sử dụng:
     * php artisan role:assign admin@example.com admin    # Gán vai trò admin
     * php artisan role:assign user@example.com seller    # Gán vai trò seller
     */
    public function handle()
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Không tìm thấy người dùng với email: {$email}");
            return 1;
        }
        
        $role = Role::where('slug', $roleName)->first();
        
        if (!$role) {
            $this->error("Không tìm thấy vai trò: {$roleName}");
            $availableRoles = Role::pluck('slug')->implode(', ');
            $this->line("Các vai trò hiện có: {$availableRoles}");
            return 1;
        }
        
        $user->assignRole($role);
        
        $this->info("Đã gán vai trò {$roleName} cho người dùng {$email} thành công");
        
        return 0;
    }
}
