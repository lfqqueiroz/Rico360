<?php
/**
 * This code was generated by
 * ___ _ _ _ _ _    _ ____    ____ ____ _    ____ ____ _  _ ____ ____ ____ ___ __   __
 *  |  | | | | |    | |  | __ |  | |__| | __ | __ |___ |\ | |___ |__/ |__|  | |  | |__/
 *  |  |_|_| | |___ | |__|    |__| |  | |    |__] |___ | \| |___ |  \ |  |  | |__| |  \
 *
 * Twilio - Assistants
 * This is the public Twilio REST API.
 *
 * NOTE: This class is auto generated by OpenAPI Generator.
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace Twilio\Rest\Assistants\V1;

use Twilio\Values;
abstract class ToolModels
{
    /**
     * @property string $description The description of the policy.
     * @property string $id The Policy ID.
     * @property string $name The name of the policy.
     * @property array $policyDetails
     * @property string $type The description of the policy.
    */
    public static function createAssistantsV1ServiceCreatePolicyRequest(array $payload = []): AssistantsV1ServiceCreatePolicyRequest
    {
        return new AssistantsV1ServiceCreatePolicyRequest($payload);
    }

    /**
     * @property string $assistantId The Assistant ID.
     * @property string $description The description of the tool.
     * @property bool $enabled True if the tool is enabled.
     * @property array $meta The metadata related to method, url, input_schema to used with the Tool.
     * @property string $name The name of the tool.
     * @property AssistantsV1ServiceCreatePolicyRequest $policy
     * @property string $type The description of the tool.
    */
    public static function createAssistantsV1ServiceCreateToolRequest(array $payload = []): AssistantsV1ServiceCreateToolRequest
    {
        return new AssistantsV1ServiceCreateToolRequest($payload);
    }

    /**
     * @property string $assistantId The Assistant ID.
     * @property string $description The description of the tool.
     * @property bool $enabled True if the tool is enabled.
     * @property array $meta The metadata related to method, url, input_schema to used with the Tool.
     * @property string $name The name of the tool.
     * @property AssistantsV1ServiceCreatePolicyRequest $policy
     * @property string $type The type of the tool.
    */
    public static function createAssistantsV1ServiceUpdateToolRequest(array $payload = []): AssistantsV1ServiceUpdateToolRequest
    {
        return new AssistantsV1ServiceUpdateToolRequest($payload);
    }

}

class AssistantsV1ServiceCreatePolicyRequest implements \JsonSerializable
{
    /**
     * @property string $description The description of the policy.
     * @property string $id The Policy ID.
     * @property string $name The name of the policy.
     * @property array $policyDetails
     * @property string $type The description of the policy.
    */
        protected $description;
        protected $id;
        protected $name;
        protected $policyDetails;
        protected $type;
    public function __construct(array $payload = []) {
        $this->description = Values::array_get($payload, 'description');
        $this->id = Values::array_get($payload, 'id');
        $this->name = Values::array_get($payload, 'name');
        $this->policyDetails = Values::array_get($payload, 'policyDetails');
        $this->type = Values::array_get($payload, 'type');
    }

    public function toArray(): array
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize(): array
    {
        return [
            'description' => $this->description,
            'id' => $this->id,
            'name' => $this->name,
            'policyDetails' => $this->policyDetails,
            'type' => $this->type
        ];
    }
}

class AssistantsV1ServiceCreateToolRequest implements \JsonSerializable
{
    /**
     * @property string $assistantId The Assistant ID.
     * @property string $description The description of the tool.
     * @property bool $enabled True if the tool is enabled.
     * @property array $meta The metadata related to method, url, input_schema to used with the Tool.
     * @property string $name The name of the tool.
     * @property AssistantsV1ServiceCreatePolicyRequest $policy
     * @property string $type The description of the tool.
    */
        protected $assistantId;
        protected $description;
        protected $enabled;
        protected $meta;
        protected $name;
        protected $policy;
        protected $type;
    public function __construct(array $payload = []) {
        $this->assistantId = Values::array_get($payload, 'assistantId');
        $this->description = Values::array_get($payload, 'description');
        $this->enabled = Values::array_get($payload, 'enabled');
        $this->meta = Values::array_get($payload, 'meta');
        $this->name = Values::array_get($payload, 'name');
        $this->policy = Values::array_get($payload, 'policy');
        $this->type = Values::array_get($payload, 'type');
    }

    public function toArray(): array
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize(): array
    {
        return [
            'assistantId' => $this->assistantId,
            'description' => $this->description,
            'enabled' => $this->enabled,
            'meta' => $this->meta,
            'name' => $this->name,
            'policy' => $this->policy,
            'type' => $this->type
        ];
    }
}

class AssistantsV1ServiceUpdateToolRequest implements \JsonSerializable
{
    /**
     * @property string $assistantId The Assistant ID.
     * @property string $description The description of the tool.
     * @property bool $enabled True if the tool is enabled.
     * @property array $meta The metadata related to method, url, input_schema to used with the Tool.
     * @property string $name The name of the tool.
     * @property AssistantsV1ServiceCreatePolicyRequest $policy
     * @property string $type The type of the tool.
    */
        protected $assistantId;
        protected $description;
        protected $enabled;
        protected $meta;
        protected $name;
        protected $policy;
        protected $type;
    public function __construct(array $payload = []) {
        $this->assistantId = Values::array_get($payload, 'assistantId');
        $this->description = Values::array_get($payload, 'description');
        $this->enabled = Values::array_get($payload, 'enabled');
        $this->meta = Values::array_get($payload, 'meta');
        $this->name = Values::array_get($payload, 'name');
        $this->policy = Values::array_get($payload, 'policy');
        $this->type = Values::array_get($payload, 'type');
    }

    public function toArray(): array
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize(): array
    {
        return [
            'assistantId' => $this->assistantId,
            'description' => $this->description,
            'enabled' => $this->enabled,
            'meta' => $this->meta,
            'name' => $this->name,
            'policy' => $this->policy,
            'type' => $this->type
        ];
    }
}

