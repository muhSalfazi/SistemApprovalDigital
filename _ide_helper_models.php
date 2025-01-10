<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $id_submission
 * @property int $auditor_id
 * @property string $status
 * @property string $approved_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Submission $submission
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Approval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval query()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereApprovedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereAuditorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereIdSubmission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Approval extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $nama_departement
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submission
 * @property-read int|null $submission_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $user
 * @property-read int|null $user_count
 * @method static \Illuminate\Database\Eloquent\Builder|Departement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Departement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Departement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereNamaDepartement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Departement extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $nama_kategori
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submission
 * @property-read int|null $submission_count
 * @method static \Illuminate\Database\Eloquent\Builder|Kategori newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Kategori newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Kategori query()
 * @method static \Illuminate\Database\Eloquent\Builder|Kategori whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kategori whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kategori whereNamaKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kategori whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Kategori extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $user
 * @property-read int|null $user_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $id_departement
 * @property int $id_kategori
 * @property int $id_user
 * @property string $title
 * @property string $no_transaksi
 * @property string $remark
 * @property string $lampiran_pdf
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read int|null $approvals_count
 * @property-read \App\Models\Departement $departement
 * @property-read \App\Models\Kategori $kategori
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Submission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereIdDepartement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereIdKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereLampiranPdf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereNoTransaksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Submission extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int $role_id
 * @property int $id_departement
 * @property string|null $last_login
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Departement $departement
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Role $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submission
 * @property-read int|null $submission_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIdDepartement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

