<?php


namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Commits
 * @package App
 */
class Commit extends Model
{
    /**
     * @var string
     */
    protected $table = 'commits';

    /**
     * @var array
     */
    protected $fillable = [
        'message', 'record_date', 'repository_id', 'author_id', 'deleted'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }

    /**
     * @param $aResponseBodyContents
     * @param $owner
     * @param $repository
     */
    public function importRecords($aResponseBodyContents, $owner, $repository)
    {

        DB::beginTransaction();

        $oOwner = Owner::where('username', $owner)->first();

        if(empty($oOwner)) {
            $oOwner = new Owner();

            $oOwner->username = $owner;

            $oOwner->save();
        }

        $oRepository = Repository::where([
            'name' => $repository,
            'owner_id' => $oOwner->id
        ])->first();

        if(empty($oRepository)) {
            $oRepository = new Repository();

            $oRepository->owner_id = $oOwner->id;
            $oRepository->name = $repository;

            $oRepository->save();
        }

        Commit::where('repository_id', $oRepository->id)
            ->update(['active' => '0']);

        foreach($aResponseBodyContents as $commitContent) {

            if(empty($commitContent->author)) {
                continue;
            }

            $author = Author::where('github_id', $commitContent->author->id)->first();

            if(empty($author)) {

                $author = new Author();

                $author->username = $commitContent->author->login;
                $author->github_id = $commitContent->author->id;
                $author->name = $commitContent->commit->author->name;

                $author->save();

            }

            $commit = new Commit();

            $commit->repository_id = $oRepository->id;
            $commit->author_id = $author->id;
            $commit->message = $commitContent->commit->message;
            $commit->record_date = $commitContent->commit->committer->date;

            $commit->save();

        }

        DB::commit();
    }

    /**
     * @param Repository $repository
     * @param Owner $owner
     * @return array
     */
    public function exportRecords(Repository $repository, Owner $owner)
    {
        $records = [
            'repository' => $repository->name,
            'owner' => $owner->username,
            'authors' => []
        ];
        $totalCommits = 0;

        $commits = $this->where([
            'repository_id' => $repository->id,
            'active' => ASSERT_ACTIVE
        ])->get();

        foreach($commits as $commit) {
            $totalCommits++;
            $records['commits'][] = [
                'message' => $commit->message,
                'date' => $commit->record_date
            ];

            $author = $commit->author;

            if(!array_key_exists($author->id, $records['authors'])) {
                $records['authors'][$author->id] = [
                    'name' => $author->name,
                    'username' => $author->username,
                    'github_id' => $author->github_id
                ];
            }

            if(!array_key_exists('total_commits', $records['authors'][$author->id])) {
                $records['authors'][$author->id]['total_commits'] = 0;
            }

            $records['authors'][$author->id]['total_commits']++;
        }

        $records['total_commits'] = $totalCommits;

        return $records;

    }
}