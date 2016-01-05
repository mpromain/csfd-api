<?php

namespace Csfd;


class Csfd
{

	/** @var Grabber */
	protected $grabber;



	public function __construct()
	{
		$this->grabber = new Grabber();
	}



	private function getSearch()
	{
		return new Search($this->grabber);
	}



	public function findMovie($filter, $returnSingle = FALSE)
	{
		return $this->getSearch()->movie($filter)->find($returnSingle);
	}



	public function findAuthor($filter, $returnSingle = FALSE)
	{
		return $this->getSearch()->author($filter)->find($returnSingle);
	}



	public function findUser($filter, $returnSingle = FALSE)
	{
		return $this->getSearch()->user($filter)->find($returnSingle);
	}



	public function getMovie($id)
	{
		return Movie::fromPage($this->grabber->request(Grabber::GET, "film/$id"), $id);
	}



	public function getAuthor($id)
	{
		return Author::fromPage($this->grabber->request(Grabber::GET, "tvurce/$id"), $id);
	}



	public function getUser($id)
	{
		return User::fromPage($this->grabber->request(Grabber::GET, "uzivatel/$id/hodnoceni"), $id);
	}



	public function getUserAllRatings($id)
	{
		$user = User::fromPage($this->grabber->request(Grabber::GET, "uzivatel/$id/hodnoceni"), $id);

		$this->grabber->queueInit();
		$total_pages = ceil($user->total_ratings / 100);
		if ($total_pages > 1)
		{
			for ($i = 2; $i <= $total_pages; ++$i) {
				$this->grabber->enqueue("uzivatel/$id/hodnoceni/strana-$i");
			}

			foreach ($this->grabber->queueRun() as $html) {
				$user->ratings = array_merge($user->ratings, User::getRatings($html));
			}
		}

		return $user;
	}

}
