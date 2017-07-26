<?php

namespace Loopkey;

/**
 * Loopkey
 *
 */
class Loopkey
{

    /**
     * Construct
     *
     */
    public function __construct()
    {
        $this->headerAuthorization  = '';
        $this->responseType         = 'json';
    }

    /**
     * Set response type
     *
     * @param $responseType string
     */
    public function setResponseType($responseType)
    {
        $this->responseType = $responseType;
    }

    /**
     * Get responseType
     *
     * @param $responseType string
     * @return bool
     */
    public function getResponseType()
    {
        return $this->responseType;
    }

    /**
     * Set Header Authorization
     *
     * @param $headerAuthorization string
     */
    public function setHeaderAuthorization($headerAuthorization)
    {
        $this->headerAuthorization = $headerAuthorization;
    }

    /**
     *
     * POST https://apiv1.loopkey.com.br/door/share
     *
     * // Headers
     * Authorization: {code}
     *
     * // POST Fields
     *
     * doorId:		 	integer 	// Door ID
     * email: 			string 		// User email
     * beginDate: 		string 		// Format: yyyy-MM-dd
     * finishDate: 		string 		// Format: yyyy-MM-dd
     * beginTime: 		string 		// Format: HH:mm
     * finishTime: 		string 		// Format: HH:mm
     * accepted: 		boolean 	// Always send true
     *
     * @return mixed
     */
    public function share($data)
    {
        $url = 'https://apiv1.loopkey.com.br/door/share';

        $headers   = [];
        $headers[] = 'Authorization: ' . $this->headerAuthorization;

        $data = [
            'doorId'        => $data['doorId'],
            'email'         => $data['email'],
            'beginDate'     => $data['beginDate'],
            'finishDate'    => $data['finishDate'],
            'beginTime'     => $data['beginTime'],
            'finishTime'    => $data['finishTime'],
            'accepted'      => true,
        ];

        return $this->response( $this->curl($url, $data, $headers) );
    }

    /**
     * Get Permissions
     *
     * @param $doorId
     * @return array|mixed|object
     */
    public function getPermissions($doorId)
    {
        $url = 'https://apiv1.loopkey.com.br/door/permissions?doorId=' . $doorId;

        $headers   = [];
        $headers[] = 'Authorization: ' . $this->headerAuthorization;

        $data = [];

        return json_decode($this->response( $this->curl($url, $data, $headers, 'GET') ));
    }

    /**
     * Remove Permissions
     *
     * @param $permissionId
     * @return array|mixed|object
     */
    public function removePermission($permissionId)
    {
        $url = 'https://apiv1.loopkey.com.br/door/removePermission?id=' . $permissionId;

        $headers   = [];
        $headers[] = 'Authorization: ' . $this->headerAuthorization;

        $data = [];

        return json_decode($this->response( $this->curl($url, $data, $headers) ));
    }

    /**
     * Remove permission by user email
     *
     * @param $doorId
     * @param $userEmail
     */
    public function removePermissionByUserEmail($doorId, $userEmail)
    {
        $permissions = $this->getPermissions($doorId);

        foreach ($permissions as $permission) {
            if ($permission->referenceValue == $userEmail ) {
                $this->removePermission($permission->id);
            }
        }
    }

    /**
     * cURL
     *
     * @param $url
     * @param $data
     * @param $headers
     * @param string $type
     * @return mixed
     */
    public function curl($url, $data, $headers, $type = 'POST')
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, "UTF-8"); // new
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);

        if ($type == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $return = curl_exec($ch);
        curl_close($ch);

        return $return;
    }

    /**
     * Returns request based on response type
     *
     * @param $return
     * @return array|mixed|object
     */
    public function response($return)
    {
        if($this->responseType == 'array') return json_decode($return, true);
        else                               return $return; // == 'json'
    }

}
