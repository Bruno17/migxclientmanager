<?php

/**
 * XdbEdit
 *
 * Copyright 2010 by Bruno Perner <b.perner@gmx.de>
 *
 * This file is part of XdbEdit, for editing custom-tables in MODx Revolution CMP.
 *
 * XdbEdit is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * XdbEdit is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * XdbEdit; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA 
 *
 * @package xdbedit
 */
/**
 * Update and Create-processor for xdbedit
 *
 * @package xdbedit
 * @subpackage processors
 */
//if (!$modx->hasPermission('quip.thread_view')) return $modx->error->failure($modx->lexicon('access_denied'));


if (empty($scriptProperties['object_id'])) {
    $updateerror = true;
    $errormsg = $modx->lexicon('quip.thread_err_ns');
    return;
}

$config = $modx->migx->customconfigs;

$includeTVList = $modx->getOption('includeTVList', $config, '');
$includeTVList = !empty($includeTVList) ? explode(',', $includeTVList) : array();
$includeTVs = $modx->getOption('includeTVs', $config, false);
$classname = 'modResource';

//$saveTVs = false;
/*
if ($modx->lexicon) {
$modx->lexicon->load($packageName . ':default');
}
*/
if (isset($scriptProperties['data'])) {
    //$scriptProperties = array_merge($scriptProperties, $modx->fromJson($scriptProperties['data']));
    $data = $modx->fromJson($scriptProperties['data']);
}

$data['id'] = $modx->getOption('object_id', $scriptProperties, null);

$parent = $modx->getOption('resource_id', $scriptProperties, false);
$checkresponse = true;

$task = $modx->getOption('task', $scriptProperties, '');

switch ($task) {
    case 'publish':
        //$response = $modx->runProcessor('resource/publish', $data);
        break;
    case 'unpublish':
        //$response = $modx->runProcessor('resource/unpublish', $data);
        break;
    case 'delete':
        //$response = $modx->runProcessor('resource/delete', $data);
        break;

    default:

        //$modx->migx->loadConfigs();
        //$tabs = $modx->migx->getTabs();


        if ($scriptProperties['object_id'] == 'new') {
            //$object = $modx->newObject($classname);
            /*
            if (!empty($parent)) {
                $data['parent'] = $parent;
            }
            $response = $modx->runProcessor('resource/create', $data);
            */
        } else {
            $extended = array();

            foreach ($data as $key => $value) {
                $field = explode('.', $key);

                if (count($field) > 1) {
                    //extended field (json-array)
                    if ($field[0] == 'Profile_extended'){
                        $extended[$field[1]] = $value;
                        unset($data[$key]); 
                    }
                }
                elseif(substr($key,0,8) == 'Profile_'){
                    $data[substr($key,8)] = $value;
                    unset($data[$key]);    
                } 

            }
            $data['extended'] = $extended;

            //$data['email'] = $data['Profile_email'];

            $response = $modx->runProcessor('security/user/update', $data);
            $checkresponse = true;
        }
}

if ($checkresponse) {
    if ($response->isError()) {
        $updateerror = true;
        $errormsg = $response->getMessage();
    }
    $object = $response->getObject();
}
