<?php

include_once $_SERVER['DOCUMENT_ROOT']."/classes/simple_html_dom.php";

class SearchResult {

	private $id;
	private $name;
	private $info;
	private $imageURL;

	// Constants
	public const IMG_NULL = "media/image_not_found.png";
	public const IMDb_SEARCH_URL = "https://www.imdb.com/find?s=tt&q=";
	
	# Getters
	public function getID() { return $this->id; }
	public function getName() { return str_replace("\'", "'", $this->name); }
	public function getInfo() { return str_replace("\'", "'", $this->info); }
	public function getImageURL() { return $this->imageURL; }

	public function init($id, $name, $info, $imageURL) {
		$this->id = $id;
		$this->name = str_replace("'", "\'", $name);
		$this->info = str_replace("'", "\'", $info);
		$this->imageURL = $imageURL;
	}

	public function searchMovie($movieName) {
		$url = self::IMDb_SEARCH_URL.str_replace(" ", "+", $movieName);
		$html = file_get_html($url);

		$movieSearchResult = array();
		foreach($html->find("tr.findResult") as $movie) {

			$td_result_text = $movie->find("td.result_text", 0);
			$td_primary_photo = $movie->find("td.primary_photo", 0);
			if($td_result_text == null
				|| $td_primary_photo == null) {
				continue;
			}

			$td_result_text_a = $td_result_text->find("a", 0);
			if($td_result_text_a == null) {
				continue;
			}

			$imageURL = self::IMG_NULL;
			$td_primary_photo_a = $td_primary_photo->find("a", 0);
			if($td_primary_photo_a != null) {
				$td_primary_photo_a_img = $td_primary_photo_a->find("img", 0);
				if($td_primary_photo_a_img != null) {
					$imageURL = $td_primary_photo_a_img->src;
				}
			}

			$name = trim($td_result_text_a->plaintext);
			$info = trim(str_replace($name, "", $td_result_text->plaintext));

			$auxURL = str_replace("/title/", "", $td_primary_photo_a->href);
			$id = explode("/", $auxURL)[0];

			$newResult = new SearchResult();
			$newResult->init($id, $name, $info, $imageURL);
			array_push($movieSearchResult, $newResult);
		}

		return $movieSearchResult;
	}

}

?>