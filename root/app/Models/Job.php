<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;//Eloquent モデルファクトリ を使うためのトレイト。Job::factory() のようにしてテストデータやダミーデータを簡単に作成できる。例）Job::factory()->create(['name' => 'エンジニア']);
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;//「論理削除（soft delete）」を使うためのトレイト。deleted_at カラムがあると、削除してもレコード自体はDBに残り、検索時に無視される。

class Job extends Model//jobsテーブルとやり取りするモデル
{
    use HasFactory;
    use SoftDeletes;
    /**
     * 複数代入可能な属性
     *
     * @var array
     */
    protected $fillable = ['name'];//モデルに一括代入（mass assignment）可能な属性を定義。
    // 例）Job::create(['name' => 'デザイナー']); // OK
    // 例）Job::create(['id' => 99, 'name' => 'ハッカー']); // id は fillable じゃない → 無視される

}
