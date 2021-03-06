<?php

namespace Doggo\Controller;

use Doggo\Model\Park;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use Silverstripe\Assets\Image;

class ParkController extends Controller
{
    private static $allowed_actions = [
        'index',
        'upload'
    ];

    private $valid_types = [
        'image/jpeg',
        'image/png'
    ];

    public function index(HTTPRequest $request)
    {
        if (!$request->isGET()) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $id = $request->param('ID');

        if (empty($id)) {
            $parks = Park::get()->toArray();
            return $this->json($parks);
        }

        $park = Park::get_by_id($id);

        if (!$park) {
            return $this->json(['error' => 'Park does not exist'], 404);
        }

        return $this->json($park);
    }

    public function upload(HTTPRequest $request) {
        if (!$request->isPOST()) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $id = $request->param('ID');

        if (empty($id)) {
            return $this->json(['error' => 'Park does not exist'], 405);
        }

        $park = Park::get_by_id($id);

        if (!$park) {
            return $this->json(['error' => 'Park does not exist'], 405);
        }

        $image = $request->postVar('image');
        if (!in_array($image['type'], $this->valid_types)) {
            return $this->json(['error' => 'File type not allowed'], 405);
        }

        if (!empty($park->getField('Photo')->exists()))
            $park->getField('Photo')->delete();

        $filedata = file_get_contents($image['tmp_name']);
        $filename = 'park/' . $id . '/' . $image['name'];
        $photo = Image::create();
        $photo->setFromString($filedata, $filename);
        $photo->write();

        $park->setField('Photo', $photo);
        $park->setField('UnderModeration', true);
        $park->write();

        return $this->json($photo->FitMax(400, 400)->getURL());
    }

    /**
     * @param $data
     * @param int $status
     * @param bool $forceObject
     * @return HTTPResponse
     */
    public function json($data, $status = 200, $forceObject = false)
    {
        $flags = null;

        if ($forceObject) {
            $flags = JSON_FORCE_OBJECT;
        }

        $response = (new HTTPResponse())
            ->setStatusCode($status)
            ->setBody(json_encode($data, $flags))
            ->addHeader('Content-Type', 'application/json');

        return $response;
    }
}
