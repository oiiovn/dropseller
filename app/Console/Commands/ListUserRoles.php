<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class ListUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:list {--user= : Filter by user email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists all users and their roles';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * Command này dùng để:
     * - Liệt kê tất cả người dùng và vai trò của họ trong hệ thống
     * - Giúp admin kiểm tra nhanh các quyền đã được phân bổ
     * - Lọc người dùng theo email để tìm kiếm nhanh
     * 
     * Cách sử dụng:
     * php artisan role:list              # Liệt kê tất cả người dùng
     * php artisan role:list --user=admin@example.com  # Lọc theo email
     */
    public function handle()
    {
        $userEmail = $this->option('user');
        
        $query = User::with('roles');
        
        if ($userEmail) {
            $query->where('email', 'like', "%{$userEmail}%");
        }
        
        $users = $query->get();
        
        if ($users->isEmpty()) {
            $this->info('Không tìm thấy người dùng nào.');
            return 0;
        }
        
        $headers = ['ID', 'Tên', 'Email', 'Vai trò'];
        
        $rows = [];
        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->implode(', ');
            $rows[] = [$user->id, $user->name, $user->email, $roles ?: 'Không có vai trò'];
        }
        
        $this->table($headers, $rows);
        
        return 0;
    }
}
