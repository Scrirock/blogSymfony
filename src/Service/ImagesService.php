<?php


namespace App\Service;


use Exception;

class ImagesService {

    private string $linkAvatar = "build/images/avatar/";
    private string $linkCover = "build/images/cover/";

    public function setAvatar($file = null): string {
        if ($file) {

            $name = $file->getClientOriginalName();
            $file->move('../assets/images/avatar', $name);

            return $this->linkAvatar.$name;
        }
        else {
            try {
                $random = random_int(1, 12);
            } catch (Exception $e) {
                $random = 1;
            }

            return $this->linkAvatar.$random.".jpg";
        }
    }

    public function setCover($file = null): string {
        if ($file) {
            $name = $file->getClientOriginalName();
            $file->move('../assets/images/cover', $name);

            return $this->linkCover.$name;
        }
        else {
            return $this->linkCover."noImageFound.png";
        }
    }

}