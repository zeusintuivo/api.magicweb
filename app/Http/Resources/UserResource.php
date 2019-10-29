<?php

namespace App\Http\Resources;

use App\Models\Role;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'email'      => $this->email,
            'api_token'  => $this->api_token,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'full_name'  => "{$this->first_name} {$this->last_name}",
            'verified'   => $this->verified,
            'active'     => $this->active,
            'gdpr'       => $this->gdpr,
            'news'       => $this->news,
        ];
    }

    protected function _selectWithSubRoles()
    {
        // Let's constuct the ids, and take 'guest' on board
        $guestId = Role::whereName('guest')->first()->id;
        $list = join(",", $this->roles->pluck('id')->prepend($guestId)->unique()->all());
        return collect(DB::select("SELECT node.name
            FROM roles AS node, roles AS parent, roles AS sub_parent, (
                SELECT node.name FROM roles AS node, roles AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt AND node.id IN ($list)
                GROUP BY node.name ORDER BY node.lft
            ) AS sub_tree
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
                AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
                AND sub_parent.name = sub_tree.name
            GROUP BY node.name ORDER BY node.lft;
        "))->pluck('name')->all();
    }
}
