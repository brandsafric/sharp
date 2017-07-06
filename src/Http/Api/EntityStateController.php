<?php

namespace Code16\Sharp\Http\Api;

class EntityStateController extends ApiController
{

    /**
     * @param string $entityKey
     * @param string $instanceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($entityKey, $instanceId)
    {
        $this->checkAuthorization("update", $entityKey, $instanceId);

        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $stateValue = request("value");

        $returned = $list->entityStateHandler()->update($instanceId, $stateValue);

        return response()->json(array_merge($returned, ["value" => $stateValue]));
    }
}