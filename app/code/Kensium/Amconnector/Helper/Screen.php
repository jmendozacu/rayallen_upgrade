<?php
namespace Kensium\Amconnector\Helper;

use SoapClient;

class ProcessResult {
  public $Status; // ProcessStatus
  public $Seconds; // int
  public $Message; // string
}

class ProcessStatus {
  const NotExists = 'NotExists';
  const InProcess = 'InProcess';
  const Completed = 'Completed';
  const Aborted = 'Aborted';
}

class GetScenario {
  public $scenario; // string
}

class GetScenarioResponse {
  public $GetScenarioResult; // ArrayOfCommand
}

class Command {
  public $FieldName; // string
  public $ObjectName; // string
  public $Value; // string
  public $Commit; // boolean
  public $IgnoreError; // boolean
  public $LinkedCommand; // Command
  public $Descriptor; // ElementDescriptor
  //Workaround for PHP BUG 50675
  function __clone(){
    foreach($this as $name => $value){
        if(gettype($value)=='object'){
            $this->$name= clone($this->$name);
        }
    }
  }
}

class ElementDescriptor {
  public $DisplayName; // string
  public $IsDisabled; // boolean
  public $IsRequired; // boolean
  public $ElementType; // ElementTypes
  public $LengthLimit; // int
  public $InputMask; // string
  public $DisplayRules; // string
  public $AllowedValues; // ArrayOfString
}

class ElementTypes {
  const String = 'String';
  const AsciiString = 'AsciiString';
  const StringSelector = 'StringSelector';
  const ExplicitSelector = 'ExplicitSelector';
  const Number = 'Number';
  const Option = 'Option';
  const WideOption = 'WideOption';
  const Calendar = 'Calendar';
  const Action = 'Action';
}

class SchemaMode {
  const Basic = 'Basic';
  const Detailed = 'Detailed';
}

class EveryValue extends Command {
}

class Key extends Command {
}

class Action extends Command {
}

class Field extends Command {
}

class Value extends Command {
  public $Message; // string
  public $IsError; // boolean
}

class Answer extends Command {
}

class RowNumber extends Command {
}

class NewRow extends Command {
}

class DeleteRow {
}

class Parameter extends Command {
}

class Attachment {
}

class Filter {
  public $Field; // Field
  public $Condition; // FilterCondition
  public $Value; // anyType
  public $Value2; // anyType
  public $OpenBrackets; // int
  public $CloseBrackets; // int
  public $Operator; // FilterOperator
}

class FilterCondition {
  const Equals = 'Equals';
  const NotEqual = 'NotEqual';
  const Greater = 'Greater';
  const GreaterOrEqual = 'GreaterOrEqual';
  const Less = 'Less';
  const LessOrEqual = 'LessOrEqual';
  const Contain = 'Contain';
  const StartsWith = 'StartsWith';
  const EndsWith = 'EndsWith';
  const NotContain = 'NotContain';
  const Between = 'Between';
  const IsNull = 'IsNull';
  const IsNotNull = 'IsNotNull';
}

class FilterOperator {
  const _And = 'And';
  const _Or = 'Or';
}

class Login {
  public $name; // string
  public $password; // string
}

class LoginResult {
  public $Code; // ErrorCode
  public $Message; // string
  public $Session; // string
}

class ErrorCode {
  const OK = 'OK';
  const INVALID_CREDENTIALS = 'INVALID_CREDENTIALS';
  const INTERNAL_ERROR = 'INTERNAL_ERROR';
  const INVALID_API_VERSION = 'INVALID_API_VERSION';
}

class LoginResponse {
  public $LoginResult; // LoginResult
}

class Logout {
}

class LogoutResponse {
}

class SetBusinessDate {
  public $date; // dateTime
}

class SetBusinessDateResponse {
}

class SetLocaleName {
  public $localeName; // string
}

class SetLocaleNameResponse {
}

class SetSchemaMode {
  public $mode; // SchemaMode
}

class SetSchemaModeResponse {
}

class GI000020Content {
  public $Actions; // GI000020Actions
  public $Filter_; // GI000020Filter_
  public $Result; // GI000020Result
  public $EnterKeys; // GI000020EnterKeys
  public $ValuesForUpdate; // GI000020ValuesForUpdate
}

class GI000020Actions {
  public $Cancel; // Action
  public $Insert; // Action
  public $EditDetail; // Action
  public $ActionsMenu; // Action
}

class GI000020Filter_ {
  public $DisplayName; // string
  public $Date; // Field
  public $ServiceCommands; // GI000020Filter_ServiceCommands
}

class GI000020Filter_ServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GI000020Result {
  public $DisplayName; // string
  public $Selected; // Field
  public $RowNumber; // Field
  public $InventoryID; // Field
  public $Description; // Field
  public $NoteDocFileID; // Field
  public $Name; // Field
  public $CreationTime; // Field
  public $NoteText; // Field
  public $ServiceCommands; // GI000020ResultServiceCommands
}

class GI000020ResultServiceCommands {
  public $KeyInventoryID; // Key
  public $KeyNoteDocFileID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GI000020EnterKeys {
  public $DisplayName; // string
  public $FieldName; // Field
  public $Key; // Field
  public $Value; // Field
  public $ServiceCommands; // GI000020EnterKeysServiceCommands
}

class GI000020EnterKeysServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GI000020ValuesForUpdate {
  public $DisplayName; // string
  public $Selected; // Field
  public $FieldName; // Field
  public $Name; // Field
  public $Value; // Field
  public $ServiceCommands; // GI000020ValuesForUpdateServiceCommands
}

class GI000020ValuesForUpdateServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GI000020Clear {
}

class GI000020ClearResponse {
}

class GI000020GetProcessStatus {
}

class GI000020GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class GI000020GetSchema {
}

class GI000020GetSchemaResponse {
  public $GetSchemaResult; // GI000020Content
}

class GI000020SetSchema {
  public $schema; // GI000020Content
}

class GI000020SetSchemaResponse {
}

class GI000020Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class GI000020ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class GI000020Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class GI000020ImportResponse {
  public $ImportResult; // GI000020ArrayOfImportResult
}

class GI000020ImportResult {
  public $Processed; // boolean
  public $Error; // string
}

class GI000020ArrayOfImportResult {
  public $ImportResult; // GI000020ImportResult
}

class GI000020Submit {
  public $commands; // ArrayOfCommand
}

class GI000020ArrayOfContent {
  public $Content; // GI000020Content
}

class GI000020SubmitResponse {
  public $SubmitResult; // GI000020ArrayOfContent
}

class GIKEMS05Content {
  public $Actions; // GIKEMS05Actions
  public $Filter_; // GIKEMS05Filter_
  public $Result; // GIKEMS05Result
  public $EnterKeys; // GIKEMS05EnterKeys
  public $ValuesForUpdate; // GIKEMS05ValuesForUpdate
}

class GIKEMS05Actions {
  public $Cancel; // Action
  public $Insert; // Action
  public $EditDetail; // Action
  public $ActionsMenu; // Action
}

class GIKEMS05Filter_ {
  public $DisplayName; // string
  public $From; // Field
  public $ServiceCommands; // GIKEMS05Filter_ServiceCommands
}

class GIKEMS05Filter_ServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS05Result {
  public $DisplayName; // string
  public $Selected; // Field
  public $RowNumber; // Field
  public $CustomerCount0; // Field
  public $Status; // Field
  public $ServiceCommands; // GIKEMS05ResultServiceCommands
}

class GIKEMS05ResultServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS05EnterKeys {
  public $DisplayName; // string
  public $FieldName; // Field
  public $Key; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS05EnterKeysServiceCommands
}

class GIKEMS05EnterKeysServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS05ValuesForUpdate {
  public $DisplayName; // string
  public $Selected; // Field
  public $FieldName; // Field
  public $Name; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS05ValuesForUpdateServiceCommands
}

class GIKEMS05ValuesForUpdateServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS05Clear {
}

class GIKEMS05ClearResponse {
}

class GIKEMS05GetProcessStatus {
}

class GIKEMS05GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class GIKEMS05GetSchema {
}

class GIKEMS05GetSchemaResponse {
  public $GetSchemaResult; // GIKEMS05Content
}

class GIKEMS05SetSchema {
  public $schema; // GIKEMS05Content
}

class GIKEMS05SetSchemaResponse {
}

class GIKEMS05Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class GIKEMS05ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class GIKEMS05Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class GIKEMS05ImportResponse {
  public $ImportResult; // GIKEMS05ArrayOfImportResult
}

class GIKEMS05ImportResult {
  public $Processed; // boolean
  public $Error; // string
}

class GIKEMS05ArrayOfImportResult {
  public $ImportResult; // GIKEMS05ImportResult
}

class GIKEMS05Submit {
  public $commands; // ArrayOfCommand
}

class GIKEMS05ArrayOfContent {
  public $Content; // GIKEMS05Content
}

class GIKEMS05SubmitResponse {
  public $SubmitResult; // GIKEMS05ArrayOfContent
}

class GIKEMS06Content {
  public $Actions; // GIKEMS06Actions
  public $Filter_; // GIKEMS06Filter_
  public $Result; // GIKEMS06Result
  public $EnterKeys; // GIKEMS06EnterKeys
  public $ValuesForUpdate; // GIKEMS06ValuesForUpdate
}

class GIKEMS06Actions {
  public $Cancel; // Action
  public $Insert; // Action
  public $EditDetail; // Action
  public $ActionsMenu; // Action
}

class GIKEMS06Filter_ {
  public $DisplayName; // string
  public $FromDate; // Field
  public $ServiceCommands; // GIKEMS06Filter_ServiceCommands
}

class GIKEMS06Filter_ServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS06Result {
  public $DisplayName; // string
  public $Selected; // Field
  public $RowNumber; // Field
  public $CategoryIDINCategoryCount0; // Field
  public $ServiceCommands; // GIKEMS06ResultServiceCommands
}

class GIKEMS06ResultServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS06EnterKeys {
  public $DisplayName; // string
  public $FieldName; // Field
  public $Key; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS06EnterKeysServiceCommands
}

class GIKEMS06EnterKeysServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS06ValuesForUpdate {
  public $DisplayName; // string
  public $Selected; // Field
  public $FieldName; // Field
  public $Name; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS06ValuesForUpdateServiceCommands
}

class GIKEMS06ValuesForUpdateServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS06Clear {
}

class GIKEMS06ClearResponse {
}

class GIKEMS06GetProcessStatus {
}

class GIKEMS06GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class GIKEMS06GetSchema {
}

class GIKEMS06GetSchemaResponse {
  public $GetSchemaResult; // GIKEMS06Content
}

class GIKEMS06SetSchema {
  public $schema; // GIKEMS06Content
}

class GIKEMS06SetSchemaResponse {
}

class GIKEMS06Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class GIKEMS06ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class GIKEMS06Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class GIKEMS06ImportResponse {
  public $ImportResult; // GIKEMS06ArrayOfImportResult
}

class GIKEMS06ImportResult {
  public $Processed; // boolean
  public $Error; // string
}

class GIKEMS06ArrayOfImportResult {
  public $ImportResult; // GIKEMS06ImportResult
}

class GIKEMS06Submit {
  public $commands; // ArrayOfCommand
}

class GIKEMS06ArrayOfContent {
  public $Content; // GIKEMS06Content
}

class GIKEMS06SubmitResponse {
  public $SubmitResult; // GIKEMS06ArrayOfContent
}

class GIKEMS07Content {
  public $Actions; // GIKEMS07Actions
  public $Filter_; // GIKEMS07Filter_
  public $Result; // GIKEMS07Result
  public $EnterKeys; // GIKEMS07EnterKeys
  public $ValuesForUpdate; // GIKEMS07ValuesForUpdate
}

class GIKEMS07Actions {
  public $Cancel; // Action
  public $Insert; // Action
  public $EditDetail; // Action
  public $ActionsMenu; // Action
}

class GIKEMS07Filter_ {
  public $DisplayName; // string
  public $FromDate; // Field
  public $ServiceCommands; // GIKEMS07Filter_ServiceCommands
}

class GIKEMS07Filter_ServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS07Result {
  public $DisplayName; // string
  public $Selected; // Field
  public $RowNumber; // Field
  public $SOOrderCount0; // Field
  public $OrderType; // Field
  public $ServiceCommands; // GIKEMS07ResultServiceCommands
}

class GIKEMS07ResultServiceCommands {
  public $KeyOrderType; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS07EnterKeys {
  public $DisplayName; // string
  public $FieldName; // Field
  public $Key; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS07EnterKeysServiceCommands
}

class GIKEMS07EnterKeysServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS07ValuesForUpdate {
  public $DisplayName; // string
  public $Selected; // Field
  public $FieldName; // Field
  public $Name; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS07ValuesForUpdateServiceCommands
}

class GIKEMS07ValuesForUpdateServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS07Clear {
}

class GIKEMS07ClearResponse {
}

class GIKEMS07GetProcessStatus {
}

class GIKEMS07GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class GIKEMS07GetSchema {
}

class GIKEMS07GetSchemaResponse {
  public $GetSchemaResult; // GIKEMS07Content
}

class GIKEMS07SetSchema {
  public $schema; // GIKEMS07Content
}

class GIKEMS07SetSchemaResponse {
}

class GIKEMS07Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class GIKEMS07ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class GIKEMS07Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class GIKEMS07ImportResponse {
  public $ImportResult; // GIKEMS07ArrayOfImportResult
}

class GIKEMS07ImportResult {
  public $Processed; // boolean
  public $Error; // string
}

class GIKEMS07ArrayOfImportResult {
  public $ImportResult; // GIKEMS07ImportResult
}

class GIKEMS07Submit {
  public $commands; // ArrayOfCommand
}

class GIKEMS07ArrayOfContent {
  public $Content; // GIKEMS07Content
}

class GIKEMS07SubmitResponse {
  public $SubmitResult; // GIKEMS07ArrayOfContent
}

class GIKEMS08Content {
  public $Actions; // GIKEMS08Actions
  public $Filter_; // GIKEMS08Filter_
  public $Result; // GIKEMS08Result
  public $EnterKeys; // GIKEMS08EnterKeys
  public $ValuesForUpdate; // GIKEMS08ValuesForUpdate
}

class GIKEMS08Actions {
  public $Cancel; // Action
  public $Insert; // Action
  public $EditDetail; // Action
  public $ActionsMenu; // Action
}

class GIKEMS08Filter_ {
  public $DisplayName; // string
  public $From; // Field
  public $ServiceCommands; // GIKEMS08Filter_ServiceCommands
}

class GIKEMS08Filter_ServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS08Result {
  public $DisplayName; // string
  public $Selected; // Field
  public $RowNumber; // Field
  public $InventoryIDInventoryItemCount0; // Field
  public $StockItem; // Field
  public $ServiceCommands; // GIKEMS08ResultServiceCommands
}

class GIKEMS08ResultServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS08EnterKeys {
  public $DisplayName; // string
  public $FieldName; // Field
  public $Key; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS08EnterKeysServiceCommands
}

class GIKEMS08EnterKeysServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS08ValuesForUpdate {
  public $DisplayName; // string
  public $Selected; // Field
  public $FieldName; // Field
  public $Name; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS08ValuesForUpdateServiceCommands
}

class GIKEMS08ValuesForUpdateServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS08Clear {
}

class GIKEMS08ClearResponse {
}

class GIKEMS08GetProcessStatus {
}

class GIKEMS08GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class GIKEMS08GetSchema {
}

class GIKEMS08GetSchemaResponse {
  public $GetSchemaResult; // GIKEMS08Content
}

class GIKEMS08SetSchema {
  public $schema; // GIKEMS08Content
}

class GIKEMS08SetSchemaResponse {
}

class GIKEMS08Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class GIKEMS08ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class GIKEMS08Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class GIKEMS08ImportResponse {
  public $ImportResult; // GIKEMS08ArrayOfImportResult
}

class GIKEMS08ImportResult {
  public $Processed; // boolean
  public $Error; // string
}

class GIKEMS08ArrayOfImportResult {
  public $ImportResult; // GIKEMS08ImportResult
}

class GIKEMS08Submit {
  public $commands; // ArrayOfCommand
}

class GIKEMS08ArrayOfContent {
  public $Content; // GIKEMS08Content
}

class GIKEMS08SubmitResponse {
  public $SubmitResult; // GIKEMS08ArrayOfContent
}

class GIKEMS10Content {
  public $Actions; // GIKEMS10Actions
  public $Filter_; // GIKEMS10Filter_
  public $Result; // GIKEMS10Result
  public $EnterKeys; // GIKEMS10EnterKeys
  public $ValuesForUpdate; // GIKEMS10ValuesForUpdate
}

class GIKEMS10Actions {
  public $Cancel; // Action
  public $Insert; // Action
  public $EditDetail; // Action
  public $ActionsMenu; // Action
}

class GIKEMS10Filter_ {
  public $DisplayName; // string
  public $FromDate; // Field
  public $ServiceCommands; // GIKEMS10Filter_ServiceCommands
}

class GIKEMS10Filter_ServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS10Result {
  public $DisplayName; // string
  public $Selected; // Field
  public $RowNumber; // Field
  public $SOShipmentCount0; // Field
  public $OrderType; // Field
  public $ServiceCommands; // GIKEMS10ResultServiceCommands
}

class GIKEMS10ResultServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS10EnterKeys {
  public $DisplayName; // string
  public $FieldName; // Field
  public $Key; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS10EnterKeysServiceCommands
}

class GIKEMS10EnterKeysServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS10ValuesForUpdate {
  public $DisplayName; // string
  public $Selected; // Field
  public $FieldName; // Field
  public $Name; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS10ValuesForUpdateServiceCommands
}

class GIKEMS10ValuesForUpdateServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS10Clear {
}

class GIKEMS10ClearResponse {
}

class GIKEMS10GetProcessStatus {
}

class GIKEMS10GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class GIKEMS10GetSchema {
}

class GIKEMS10GetSchemaResponse {
  public $GetSchemaResult; // GIKEMS10Content
}

class GIKEMS10SetSchema {
  public $schema; // GIKEMS10Content
}

class GIKEMS10SetSchemaResponse {
}

class GIKEMS10Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class GIKEMS10ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class GIKEMS10Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class GIKEMS10ImportResponse {
  public $ImportResult; // GIKEMS10ArrayOfImportResult
}

class GIKEMS10ImportResult {
  public $Processed; // boolean
  public $Error; // string
}

class GIKEMS10ArrayOfImportResult {
  public $ImportResult; // GIKEMS10ImportResult
}

class GIKEMS10Submit {
  public $commands; // ArrayOfCommand
}

class GIKEMS10ArrayOfContent {
  public $Content; // GIKEMS10Content
}

class GIKEMS10SubmitResponse {
  public $SubmitResult; // GIKEMS10ArrayOfContent
}

class GIKEMS12Content {
  public $Actions; // GIKEMS12Actions
  public $Filter_; // GIKEMS12Filter_
  public $Result; // GIKEMS12Result
  public $EnterKeys; // GIKEMS12EnterKeys
  public $ValuesForUpdate; // GIKEMS12ValuesForUpdate
}

class GIKEMS12Actions {
  public $Cancel; // Action
  public $Insert; // Action
  public $EditDetail; // Action
  public $ActionsMenu; // Action
}

class GIKEMS12Filter_ {
  public $DisplayName; // string
  public $Date; // Field
  public $ServiceCommands; // GIKEMS12Filter_ServiceCommands
}

class GIKEMS12Filter_ServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS12Result {
  public $DisplayName; // string
  public $Selected; // Field
  public $RowNumber; // Field
  public $InventoryCD; // Field
  public $StkItem; // Field
  public $LastModifiedDateTime; // Field
  public $ServiceCommands; // GIKEMS12ResultServiceCommands
}

class GIKEMS12ResultServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS12EnterKeys {
  public $DisplayName; // string
  public $FieldName; // Field
  public $Key; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS12EnterKeysServiceCommands
}

class GIKEMS12EnterKeysServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS12ValuesForUpdate {
  public $DisplayName; // string
  public $Selected; // Field
  public $FieldName; // Field
  public $Name; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS12ValuesForUpdateServiceCommands
}

class GIKEMS12ValuesForUpdateServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS12Clear {
}

class GIKEMS12ClearResponse {
}

class GIKEMS12GetProcessStatus {
}

class GIKEMS12GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class GIKEMS12GetSchema {
}

class GIKEMS12GetSchemaResponse {
  public $GetSchemaResult; // GIKEMS12Content
}

class GIKEMS12SetSchema {
  public $schema; // GIKEMS12Content
}

class GIKEMS12SetSchemaResponse {
}

class GIKEMS12Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class GIKEMS12ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class GIKEMS12Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class GIKEMS12ImportResponse {
  public $ImportResult; // GIKEMS12ArrayOfImportResult
}

class GIKEMS12ImportResult {
  public $Processed; // boolean
  public $Error; // string
}

class GIKEMS12ArrayOfImportResult {
  public $ImportResult; // GIKEMS12ImportResult
}

class GIKEMS12Submit {
  public $commands; // ArrayOfCommand
}

class GIKEMS12ArrayOfContent {
  public $Content; // GIKEMS12Content
}

class GIKEMS12SubmitResponse {
  public $SubmitResult; // GIKEMS12ArrayOfContent
}

class GIKEMS13Content {
  public $Actions; // GIKEMS13Actions
  public $Filter_; // GIKEMS13Filter_
  public $Result; // GIKEMS13Result
  public $EnterKeys; // GIKEMS13EnterKeys
  public $ValuesForUpdate; // GIKEMS13ValuesForUpdate
}

class GIKEMS13Actions {
  public $Cancel; // Action
  public $Insert; // Action
  public $EditDetail; // Action
  public $ActionsMenu; // Action
}

class GIKEMS13Filter_ {
  public $DisplayName; // string
  public $Date; // Field
  public $ServiceCommands; // GIKEMS13Filter_ServiceCommands
}

class GIKEMS13Filter_ServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS13Result {
  public $DisplayName; // string
  public $Selected; // Field
  public $RowNumber; // Field
  public $Acctcd; // Field
  public $AcctName; // Field
  public $LastModifiedDateTime; // Field
  public $ServiceCommands; // GIKEMS13ResultServiceCommands
}

class GIKEMS13ResultServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS13EnterKeys {
  public $DisplayName; // string
  public $FieldName; // Field
  public $Key; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS13EnterKeysServiceCommands
}

class GIKEMS13EnterKeysServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS13ValuesForUpdate {
  public $DisplayName; // string
  public $Selected; // Field
  public $FieldName; // Field
  public $Name; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS13ValuesForUpdateServiceCommands
}

class GIKEMS13ValuesForUpdateServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS13Clear {
}

class GIKEMS13ClearResponse {
}

class GIKEMS13GetProcessStatus {
}

class GIKEMS13GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class GIKEMS13GetSchema {
}

class GIKEMS13GetSchemaResponse {
  public $GetSchemaResult; // GIKEMS13Content
}

class GIKEMS13SetSchema {
  public $schema; // GIKEMS13Content
}

class GIKEMS13SetSchemaResponse {
}

class GIKEMS13Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class GIKEMS13ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class GIKEMS13Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class GIKEMS13ImportResponse {
  public $ImportResult; // GIKEMS13ArrayOfImportResult
}

class GIKEMS13ImportResult {
  public $Processed; // boolean
  public $Error; // string
}

class GIKEMS13ArrayOfImportResult {
  public $ImportResult; // GIKEMS13ImportResult
}

class GIKEMS13Submit {
  public $commands; // ArrayOfCommand
}

class GIKEMS13ArrayOfContent {
  public $Content; // GIKEMS13Content
}

class GIKEMS13SubmitResponse {
  public $SubmitResult; // GIKEMS13ArrayOfContent
}

class GIKEMS16Content {
  public $Actions; // GIKEMS16Actions
  public $Filter_; // GIKEMS16Filter_
  public $Result; // GIKEMS16Result
  public $EnterKeys; // GIKEMS16EnterKeys
  public $ValuesForUpdate; // GIKEMS16ValuesForUpdate
}

class GIKEMS16Actions {
  public $Cancel; // Action
  public $Insert; // Action
  public $EditDetail; // Action
  public $ActionsMenu; // Action
}

class GIKEMS16Filter_ {
  public $DisplayName; // string
  public $Date; // Field
  public $Warehouse; // Field
  public $ServiceCommands; // GIKEMS16Filter_ServiceCommands
}

class GIKEMS16Filter_ServiceCommands {
  public $EveryWarehouse; // EveryValue
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS16Result {
  public $DisplayName; // string
  public $Selected; // Field
  public $RowNumber; // Field
  public $InventoryIDInventoryItemInventoryCD; // Field
  public $WarehouseID; // Field
  public $QtyOnHand; // Field
  public $QtyAvailable; // Field
  public $NoteText; // Field
  public $ServiceCommands; // GIKEMS16ResultServiceCommands
}

class GIKEMS16ResultServiceCommands {
  public $KeyInventoryIDInventoryItemInventoryCD; // Key
  public $KeyWarehouseID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS16EnterKeys {
  public $DisplayName; // string
  public $FieldName; // Field
  public $Key; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS16EnterKeysServiceCommands
}

class GIKEMS16EnterKeysServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS16ValuesForUpdate {
  public $DisplayName; // string
  public $Selected; // Field
  public $FieldName; // Field
  public $Name; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS16ValuesForUpdateServiceCommands
}

class GIKEMS16ValuesForUpdateServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS16Clear {
}

class GIKEMS16ClearResponse {
}

class GIKEMS16GetProcessStatus {
}

class GIKEMS16GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class GIKEMS16GetSchema {
}

class GIKEMS16GetSchemaResponse {
  public $GetSchemaResult; // GIKEMS16Content
}

class GIKEMS16SetSchema {
  public $schema; // GIKEMS16Content
}

class GIKEMS16SetSchemaResponse {
}

class GIKEMS16Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class GIKEMS16ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class GIKEMS16Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class GIKEMS16ImportResponse {
  public $ImportResult; // GIKEMS16ArrayOfImportResult
}

class GIKEMS16ImportResult {
  public $Processed; // boolean
  public $Error; // string
}

class GIKEMS16ArrayOfImportResult {
  public $ImportResult; // GIKEMS16ImportResult
}

class GIKEMS16Submit {
  public $commands; // ArrayOfCommand
}

class GIKEMS16ArrayOfContent {
  public $Content; // GIKEMS16Content
}

class GIKEMS16SubmitResponse {
  public $SubmitResult; // GIKEMS16ArrayOfContent
}

class GIKEMS18Content {
  public $Actions; // GIKEMS18Actions
  public $Filter_; // GIKEMS18Filter_
  public $Result; // GIKEMS18Result
  public $EnterKeys; // GIKEMS18EnterKeys
  public $ValuesForUpdate; // GIKEMS18ValuesForUpdate
}

class GIKEMS18Actions {
  public $Cancel; // Action
  public $Insert; // Action
  public $EditDetail; // Action
  public $ActionsMenu; // Action
}

class GIKEMS18Filter_ {
  public $DisplayName; // string
  public $Date; // Field
  public $ServiceCommands; // GIKEMS18Filter_ServiceCommands
}

class GIKEMS18Filter_ServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS18Result {
  public $DisplayName; // string
  public $Selected; // Field
  public $RowNumber; // Field
  public $InventoryID; // Field
  public $DefaultPrice; // Field
  public $NoteText; // Field
  public $ServiceCommands; // GIKEMS18ResultServiceCommands
}

class GIKEMS18ResultServiceCommands {
  public $KeyInventoryID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS18EnterKeys {
  public $DisplayName; // string
  public $FieldName; // Field
  public $Key; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS18EnterKeysServiceCommands
}

class GIKEMS18EnterKeysServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS18ValuesForUpdate {
  public $DisplayName; // string
  public $Selected; // Field
  public $FieldName; // Field
  public $Name; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS18ValuesForUpdateServiceCommands
}

class GIKEMS18ValuesForUpdateServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS18Clear {
}

class GIKEMS18ClearResponse {
}

class GIKEMS18GetProcessStatus {
}

class GIKEMS18GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class GIKEMS18GetSchema {
}

class GIKEMS18GetSchemaResponse {
  public $GetSchemaResult; // GIKEMS18Content
}

class GIKEMS18SetSchema {
  public $schema; // GIKEMS18Content
}

class GIKEMS18SetSchemaResponse {
}

class GIKEMS18Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class GIKEMS18ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class GIKEMS18Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class GIKEMS18ImportResponse {
  public $ImportResult; // GIKEMS18ArrayOfImportResult
}

class GIKEMS18ImportResult {
  public $Processed; // boolean
  public $Error; // string
}

class GIKEMS18ArrayOfImportResult {
  public $ImportResult; // GIKEMS18ImportResult
}

class GIKEMS18Submit {
  public $commands; // ArrayOfCommand
}

class GIKEMS18ArrayOfContent {
  public $Content; // GIKEMS18Content
}

class GIKEMS18SubmitResponse {
  public $SubmitResult; // GIKEMS18ArrayOfContent
}

class GIKEMS19Content {
  public $Actions; // GIKEMS19Actions
  public $Result; // GIKEMS19Result
  public $EnterKeys; // GIKEMS19EnterKeys
  public $ValuesForUpdate; // GIKEMS19ValuesForUpdate
}

class GIKEMS19Actions {
  public $Cancel; // Action
  public $Insert; // Action
  public $EditDetail; // Action
  public $ActionsMenu; // Action
}

class GIKEMS19Result {
  public $DisplayName; // string
  public $Selected; // Field
  public $RowNumber; // Field
  public $Description; // Field
  public $CategoryIDINCategoryCategoryID; // Field
  public $ParentCategory; // Field
  public $NoteText; // Field
  public $ServiceCommands; // GIKEMS19ResultServiceCommands
}

class GIKEMS19ResultServiceCommands {
  public $KeyCategoryIDINCategoryCategoryID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS19EnterKeys {
  public $DisplayName; // string
  public $FieldName; // Field
  public $Key; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS19EnterKeysServiceCommands
}

class GIKEMS19EnterKeysServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS19ValuesForUpdate {
  public $DisplayName; // string
  public $Selected; // Field
  public $FieldName; // Field
  public $Name; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS19ValuesForUpdateServiceCommands
}

class GIKEMS19ValuesForUpdateServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS19PrimaryKey {
  public $CategoryIDINCategoryCategoryID; // Value
}

class GIKEMS19Clear {
}

class GIKEMS19ClearResponse {
}

class GIKEMS19GetProcessStatus {
}

class GIKEMS19GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class GIKEMS19GetSchema {
}

class GIKEMS19GetSchemaResponse {
  public $GetSchemaResult; // GIKEMS19Content
}

class GIKEMS19SetSchema {
  public $schema; // GIKEMS19Content
}

class GIKEMS19SetSchemaResponse {
}

class GIKEMS19Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class GIKEMS19ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class GIKEMS19Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class GIKEMS19ImportResponse {
  public $ImportResult; // GIKEMS19ArrayOfImportResult
}

class GIKEMS19ImportResult {
  public $Processed; // boolean
  public $Error; // string
  public $Keys; // GIKEMS19PrimaryKey
}

class GIKEMS19ArrayOfImportResult {
  public $ImportResult; // GIKEMS19ImportResult
}

class GIKEMS19Submit {
  public $commands; // ArrayOfCommand
}

class GIKEMS19ArrayOfContent {
  public $Content; // GIKEMS19Content
}

class GIKEMS19SubmitResponse {
  public $SubmitResult; // GIKEMS19ArrayOfContent
}

class GIKEMS21Content {
  public $Actions; // GIKEMS21Actions
  public $Filter_; // GIKEMS21Filter_
  public $Result; // GIKEMS21Result
  public $EnterKeys; // GIKEMS21EnterKeys
  public $ValuesForUpdate; // GIKEMS21ValuesForUpdate
}

class GIKEMS21Actions {
  public $Cancel; // Action
  public $Insert; // Action
  public $EditDetail; // Action
  public $ActionsMenu; // Action
}

class GIKEMS21Filter_ {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $ServiceCommands; // GIKEMS21Filter_ServiceCommands
}

class GIKEMS21Filter_ServiceCommands {
  public $EveryInventoryID; // EveryValue
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS21Result {
  public $DisplayName; // string
  public $Selected; // Field
  public $RowNumber; // Field
  public $InventoryID; // Field
  public $Description; // Field
  public $NoteDocFileID; // Field
  public $Name; // Field
  public $NoteText; // Field
  public $ServiceCommands; // GIKEMS21ResultServiceCommands
}

class GIKEMS21ResultServiceCommands {
  public $KeyInventoryID; // Key
  public $KeyNoteDocFileID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS21EnterKeys {
  public $DisplayName; // string
  public $FieldName; // Field
  public $Key; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS21EnterKeysServiceCommands
}

class GIKEMS21EnterKeysServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS21ValuesForUpdate {
  public $DisplayName; // string
  public $Selected; // Field
  public $FieldName; // Field
  public $Name; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS21ValuesForUpdateServiceCommands
}

class GIKEMS21ValuesForUpdateServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS21Clear {
}

class GIKEMS21ClearResponse {
}

class GIKEMS21GetProcessStatus {
}

class GIKEMS21GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class GIKEMS21GetSchema {
}

class GIKEMS21GetSchemaResponse {
  public $GetSchemaResult; // GIKEMS21Content
}

class GIKEMS21SetSchema {
  public $schema; // GIKEMS21Content
}

class GIKEMS21SetSchemaResponse {
}

class GIKEMS21Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class GIKEMS21ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class GIKEMS21Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class GIKEMS21ImportResponse {
  public $ImportResult; // GIKEMS21ArrayOfImportResult
}

class GIKEMS21ImportResult {
  public $Processed; // boolean
  public $Error; // string
}

class GIKEMS21ArrayOfImportResult {
  public $ImportResult; // GIKEMS21ImportResult
}

class GIKEMS21Submit {
  public $commands; // ArrayOfCommand
}

class GIKEMS21ArrayOfContent {
  public $Content; // GIKEMS21Content
}

class GIKEMS21SubmitResponse {
  public $SubmitResult; // GIKEMS21ArrayOfContent
}

class GIKEMS22Content {
  public $Actions; // GIKEMS22Actions
  public $Filter_; // GIKEMS22Filter_
  public $Result; // GIKEMS22Result
  public $EnterKeys; // GIKEMS22EnterKeys
  public $ValuesForUpdate; // GIKEMS22ValuesForUpdate
}

class GIKEMS22Actions {
  public $Cancel; // Action
  public $Insert; // Action
  public $EditDetail; // Action
  public $ActionsMenu; // Action
}

class GIKEMS22Filter_ {
  public $DisplayName; // string
  public $CategoryCD; // Field
  public $ServiceCommands; // GIKEMS22Filter_ServiceCommands
}

class GIKEMS22Filter_ServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS22Result {
  public $DisplayName; // string
  public $Selected; // Field
  public $RowNumber; // Field
  public $Description; // Field
  public $NoteDocFileID; // Field
  public $Name; // Field
  public $NoteText; // Field
  public $ServiceCommands; // GIKEMS22ResultServiceCommands
}

class GIKEMS22ResultServiceCommands {
  public $KeyNoteDocFileID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class GIKEMS22EnterKeys {
  public $DisplayName; // string
  public $FieldName; // Field
  public $Key; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS22EnterKeysServiceCommands
}

class GIKEMS22EnterKeysServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS22ValuesForUpdate {
  public $DisplayName; // string
  public $Selected; // Field
  public $FieldName; // Field
  public $Name; // Field
  public $Value; // Field
  public $ServiceCommands; // GIKEMS22ValuesForUpdateServiceCommands
}

class GIKEMS22ValuesForUpdateServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class GIKEMS22Clear {
}

class GIKEMS22ClearResponse {
}

class GIKEMS22GetProcessStatus {
}

class GIKEMS22GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class GIKEMS22GetSchema {
}

class GIKEMS22GetSchemaResponse {
  public $GetSchemaResult; // GIKEMS22Content
}

class GIKEMS22SetSchema {
  public $schema; // GIKEMS22Content
}

class GIKEMS22SetSchemaResponse {
}

class GIKEMS22Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class GIKEMS22ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class GIKEMS22Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class GIKEMS22ImportResponse {
  public $ImportResult; // GIKEMS22ArrayOfImportResult
}

class GIKEMS22ImportResult {
  public $Processed; // boolean
  public $Error; // string
}

class GIKEMS22ArrayOfImportResult {
  public $ImportResult; // GIKEMS22ImportResult
}

class GIKEMS22Submit {
  public $commands; // ArrayOfCommand
}

class GIKEMS22ArrayOfContent {
  public $Content; // GIKEMS22Content
}

class GIKEMS22SubmitResponse {
  public $SubmitResult; // GIKEMS22ArrayOfContent
}

class IN202500Content {
  public $Actions; // IN202500Actions
  public $StockItemSummary; // IN202500StockItemSummary
  public $GeneralSettingsItemDefaults; // IN202500GeneralSettingsItemDefaults
  public $GeneralSettingsWarehouseDefaults; // IN202500GeneralSettingsWarehouseDefaults
  public $GeneralSettingsUnitOfMeasureBaseUnit; // IN202500GeneralSettingsUnitOfMeasureBaseUnit
  public $GeneralSettingsPhysicalInventory; // IN202500GeneralSettingsPhysicalInventory
  public $PriceCostInfoPriceManagement; // IN202500PriceCostInfoPriceManagement
  public $PriceCostInfoStandardCost; // IN202500PriceCostInfoStandardCost
  public $Ecommerce; // IN202500Ecommerce
  public $Attributes; // IN202500Attributes
  public $PackagingDimensions; // IN202500PackagingDimensions
  public $PackagingAutomaticPackaging; // IN202500PackagingAutomaticPackaging
  public $GLAccounts; // IN202500GLAccounts
  public $Description; // IN202500Description
  public $Subitems; // IN202500Subitems
  public $PriceCostInfoCostStatistics; // IN202500PriceCostInfoCostStatistics
  public $GeneralSettingsUnitOfMeasure; // IN202500GeneralSettingsUnitOfMeasure
  public $WarehouseDetails; // IN202500WarehouseDetails
  public $CrossReference; // IN202500CrossReference
  public $ReplenishmentInfoReplenishmentParameters; // IN202500ReplenishmentInfoReplenishmentParameters
  public $ReplenishmentInfoSubitemReplenishmentParameters; // IN202500ReplenishmentInfoSubitemReplenishmentParameters
  public $VendorDetails; // IN202500VendorDetails
  public $PackagingAutomaticPackagingBoxes; // IN202500PackagingAutomaticPackagingBoxes
  public $AttributesAttributes; // IN202500AttributesAttributes
  public $AttributesSalesCategories; // IN202500AttributesSalesCategories
  public $RestrictionGroups; // IN202500RestrictionGroups
  public $EcommerceCrossSells; // IN202500EcommerceCrossSells
  public $EcommerceUpSells; // IN202500EcommerceUpSells
  public $SpecifyNewID; // IN202500SpecifyNewID
}

class IN202500Actions {
  public $Save; // Action
  public $Cancel; // Action
  public $Insert; // Action
  public $CopyDocumentEdit; // Action
  public $PasteDocumentEdit; // Action
  public $SaveTemplateEdit; // Action
  public $Delete; // Action
  public $First; // Action
  public $Prev; // Action
  public $Next; // Action
  public $Last; // Action
  public $ChangeID; // Action
  public $UpdateCostAction; // Action
  public $ViewRestrictionGroupsAction; // Action
  public $SummaryInquiry; // Action
  public $AllocationDetailsInquiry; // Action
  public $TransactionSummaryInquiry; // Action
  public $TransactionDetailsInquiry; // Action
  public $TransactionHistoryInquiry; // Action
  public $SalesPricesInquiry; // Action
  public $VendorPricesInquiry; // Action
  public $AddWarehouseDetail; // Action
  public $UpdateReplenishment; // Action
  public $GenerateSubitems; // Action
  public $GenerateLotSerialNumber; // Action
  public $ViewGroupDetails; // Action
}

class IN202500StockItemSummary {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $ItemStatus; // Field
  public $Description; // Field
  public $ProductWorkgroup; // Field
  public $ProductManager; // Field
  public $NoteText; // Field
  public $ServiceCommands; // IN202500StockItemSummaryServiceCommands
}

class IN202500StockItemSummaryServiceCommands {
  public $KeyInventoryID; // Key
  public $EveryInventoryID; // EveryValue
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500GeneralSettingsItemDefaults {
  public $DisplayName; // string
  public $ItemClass; // Field
  public $Type; // Field
  public $IsAKit; // Field
  public $ValuationMethod; // Field
  public $TaxCategory; // Field
  public $PostingClass; // Field
  public $LotSerialClass; // Field
  public $AutoIncrementalValue; // Field
  public $ServiceCommands; // IN202500GeneralSettingsItemDefaultsServiceCommands
}

class IN202500GeneralSettingsItemDefaultsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500GeneralSettingsWarehouseDefaults {
  public $DisplayName; // string
  public $DefaultWarehouse; // Field
  public $DefaultIssueFrom; // Field
  public $DefaultReceiptTo; // Field
  public $DefaultSubitem; // Field
  public $UseOnEntry; // Field
  public $ServiceCommands; // IN202500GeneralSettingsWarehouseDefaultsServiceCommands
}

class IN202500GeneralSettingsWarehouseDefaultsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500GeneralSettingsUnitOfMeasureBaseUnit {
  public $DisplayName; // string
  public $BaseUnit; // Field
  public $SalesUnit; // Field
  public $PurchaseUnit; // Field
  public $ServiceCommands; // IN202500GeneralSettingsUnitOfMeasureBaseUnitServiceCommands
}

class IN202500GeneralSettingsUnitOfMeasureBaseUnitServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500GeneralSettingsPhysicalInventory {
  public $DisplayName; // string
  public $PICycle; // Field
  public $ABCCode; // Field
  public $FixedABCCode; // Field
  public $MovementClass; // Field
  public $FixedMovementClass; // Field
  public $ServiceCommands; // IN202500GeneralSettingsPhysicalInventoryServiceCommands
}

class IN202500GeneralSettingsPhysicalInventoryServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500PriceCostInfoPriceManagement {
  public $DisplayName; // string
  public $PriceClass; // Field
  public $PriceWorkgroup; // Field
  public $PriceManager; // Field
  public $SubjectToCommission; // Field
  public $MinMarkup; // Field
  public $Markup; // Field
  public $MSRP; // Field
  public $DefaultPrice; // Field
  public $ServiceCommands; // IN202500PriceCostInfoPriceManagementServiceCommands
}

class IN202500PriceCostInfoPriceManagementServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500PriceCostInfoStandardCost {
  public $DisplayName; // string
  public $PendingCost; // Field
  public $PendingCostDate; // Field
  public $CurrentCost; // Field
  public $EffectiveDate; // Field
  public $LastCost; // Field
  public $ServiceCommands; // IN202500PriceCostInfoStandardCostServiceCommands
}

class IN202500PriceCostInfoStandardCostServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500Ecommerce {
  public $DisplayName; // string
  public $HomePage; // Field
  public $URLKey; // Field
  public $DescriptionLong; // Field
  public $DescriptionShort; // Field
  public $AlternateSearchKeywords; // Field
  public $MetaTitle; // Field
  public $MetaDescription; // Field
  public $MetaKeywords; // Field
  public $Active; // Field
  public $UsrKNBestseller; // Field
  public $Visibility; // Field
  public $CategoryID; // Field
  public $UpSellID; // Field
  public $CrossSellID; // Field
  public $ServiceCommands; // IN202500EcommerceServiceCommands
}

class IN202500EcommerceServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500Attributes {
  public $DisplayName; // string
  public $ImageUrl; // Field
  public $ServiceCommands; // IN202500AttributesServiceCommands
}

class IN202500AttributesServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500PackagingDimensions {
  public $DisplayName; // string
  public $Weight; // Field
  public $WeightUOM; // Field
  public $Volume; // Field
  public $VolumeUOM; // Field
  public $ServiceCommands; // IN202500PackagingDimensionsServiceCommands
}

class IN202500PackagingDimensionsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500PackagingAutomaticPackaging {
  public $DisplayName; // string
  public $PackagingOption; // Field
  public $PackSeparately; // Field
  public $ServiceCommands; // IN202500PackagingAutomaticPackagingServiceCommands
}

class IN202500PackagingAutomaticPackagingServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500GLAccounts {
  public $DisplayName; // string
  public $InventoryAccount; // Field
  public $InventorySub; // Field
  public $ReasonCodeSub; // Field
  public $SalesAccount; // Field
  public $SalesSub; // Field
  public $COGSAccount; // Field
  public $COGSSub; // Field
  public $StandardCostVarianceAccount; // Field
  public $StandardCostVarianceSub; // Field
  public $StandardCostRevaluationAccount; // Field
  public $StandardCostRevaluationSub; // Field
  public $POAccrualAccount; // Field
  public $POAccrualSub; // Field
  public $PurchasePriceVarianceAccount; // Field
  public $PurchasePriceVarianceSub; // Field
  public $LandedCostVarianceAccount; // Field
  public $LandedCostVarianceSub; // Field
  public $DiscountAccount; // Field
  public $DiscountSub; // Field
  public $DeferralAccount; // Field
  public $DeferralSub; // Field
  public $ServiceCommands; // IN202500GLAccountsServiceCommands
}

class IN202500GLAccountsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500Description {
  public $DisplayName; // string
  public $Content; // Field
  public $ServiceCommands; // IN202500DescriptionServiceCommands
}

class IN202500DescriptionServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500Subitems {
  public $DisplayName; // string
  public $SegmentID; // Field
  public $Value; // Field
  public $ServiceCommands; // IN202500SubitemsServiceCommands
}

class IN202500SubitemsServiceCommands {
  public $KeySegmentID; // Key
  public $KeyValue; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class IN202500PriceCostInfoCostStatistics {
  public $DisplayName; // string
  public $LastCost; // Field
  public $AverageCost; // Field
  public $MinCost; // Field
  public $MaxCost; // Field
  public $ServiceCommands; // IN202500PriceCostInfoCostStatisticsServiceCommands
}

class IN202500PriceCostInfoCostStatisticsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500GeneralSettingsUnitOfMeasure {
  public $DisplayName; // string
  public $FromUnit; // Field
  public $MultiplyDivide; // Field
  public $ConversionFactor; // Field
  public $ToUnitSampleToUnit; // Field
  public $ServiceCommands; // IN202500GeneralSettingsUnitOfMeasureServiceCommands
}

class IN202500GeneralSettingsUnitOfMeasureServiceCommands {
  public $KeyFromUnit; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class IN202500WarehouseDetails {
  public $DisplayName; // string
  public $Default; // Field
  public $Warehouse; // Field
  public $WarehouseWarehouseID; // Field
  public $DefaultReceiptTo; // Field
  public $DefaultIssueFrom; // Field
  public $Status; // Field
  public $InventoryAccount; // Field
  public $InventorySub; // Field
  public $ProductWorkgroup; // Field
  public $ProductManager; // Field
  public $OverrideStdCost; // Field
  public $PriceOverride; // Field
  public $QtyOnHand; // Field
  public $OverridePreferredVendor; // Field
  public $PreferredVendor; // Field
  public $OverrideReplenishmentSettings; // Field
  public $Seasonality; // Field
  public $ReplenishmentSource; // Field
  public $ReplenishmentWarehouse; // Field
  public $Override; // Field
  public $ServiceLevel; // Field
  public $LastForecastDate; // Field
  public $DailyDemandForecast; // Field
  public $DailyDemandForecastErrorSTDEV; // Field
  public $NoteText; // Field
  public $ServiceCommands; // IN202500WarehouseDetailsServiceCommands
}

class IN202500WarehouseDetailsServiceCommands {
  public $KeyWarehouse; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500CrossReference {
  public $DisplayName; // string
  public $AlternateType; // Field
  public $VendorCustomer; // Field
  public $AlternateID; // Field
  public $Description; // Field
  public $ServiceCommands; // IN202500CrossReferenceServiceCommands
}

class IN202500CrossReferenceServiceCommands {
  public $KeyAlternateType; // Key
  public $KeyVendorCustomer; // Key
  public $KeyAlternateID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class IN202500ReplenishmentInfoReplenishmentParameters {
  public $DisplayName; // string
  public $ReplClass; // Field
  public $Seasonality; // Field
  public $Source; // Field
  public $Method; // Field
  public $ReplenishmentWarehouse; // Field
  public $MaxShelfLifeDays; // Field
  public $LaunchDate; // Field
  public $TerminationDate; // Field
  public $ServiceLevel; // Field
  public $SafetyStock; // Field
  public $ReorderPoint; // Field
  public $MaxQty; // Field
  public $TransferERQ; // Field
  public $DemandForecastModel; // Field
  public $ForecastPeriodType; // Field
  public $PeriodsToAnalyze; // Field
  public $ServiceCommands; // IN202500ReplenishmentInfoReplenishmentParametersServiceCommands
}

class IN202500ReplenishmentInfoReplenishmentParametersServiceCommands {
  public $KeyReplClass; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class IN202500ReplenishmentInfoSubitemReplenishmentParameters {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $ReplenishmentClassID; // Field
  public $SafetyStock; // Field
  public $ReorderPoint; // Field
  public $MaxQty; // Field
  public $TransferERQ; // Field
  public $Status; // Field
  public $ServiceCommands; // IN202500ReplenishmentInfoSubitemReplenishmentParametersServiceCommands
}

class IN202500ReplenishmentInfoSubitemReplenishmentParametersServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class IN202500VendorDetails {
  public $DisplayName; // string
  public $Active; // Field
  public $Default; // Field
  public $VendorID; // Field
  public $VendorName; // Field
  public $Warehouse; // Field
  public $PurchaseUnit; // Field
  public $VendorInventoryID; // Field
  public $LeadTimeDays; // Field
  public $Override; // Field
  public $AddLeadTimeDays; // Field
  public $MinOrderFreqDays; // Field
  public $MinOrderQty; // Field
  public $MaxOrderQty; // Field
  public $LotSize; // Field
  public $EOQ; // Field
  public $CurrencyID; // Field
  public $LastVendorPrice; // Field
  public $ServiceCommands; // IN202500VendorDetailsServiceCommands
}

class IN202500VendorDetailsServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class IN202500PackagingAutomaticPackagingBoxes {
  public $DisplayName; // string
  public $BoxID; // Field
  public $Description; // Field
  public $UOM; // Field
  public $Qty; // Field
  public $MaxWeight; // Field
  public $MaxVolume; // Field
  public $MaxQty; // Field
  public $ServiceCommands; // IN202500PackagingAutomaticPackagingBoxesServiceCommands
}

class IN202500PackagingAutomaticPackagingBoxesServiceCommands {
  public $KeyBoxID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class IN202500AttributesAttributes {
  public $DisplayName; // string
  public $Attribute; // Field
  public $Required; // Field
  public $Value; // Field
  public $ServiceCommands; // IN202500AttributesAttributesServiceCommands
}

class IN202500AttributesAttributesServiceCommands {
  public $KeyAttribute; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class IN202500AttributesSalesCategories {
  public $DisplayName; // string
  public $CategoryID; // Field
  public $ServiceCommands; // IN202500AttributesSalesCategoriesServiceCommands
}

class IN202500AttributesSalesCategoriesServiceCommands {
  public $KeyCategoryID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class IN202500RestrictionGroups {
  public $DisplayName; // string
  public $Included; // Field
  public $GroupName; // Field
  public $SpecificType; // Field
  public $Description; // Field
  public $Active; // Field
  public $GroupType; // Field
  public $ServiceCommands; // IN202500RestrictionGroupsServiceCommands
}

class IN202500RestrictionGroupsServiceCommands {
  public $KeyGroupName; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class IN202500EcommerceCrossSells {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $Description; // Field
  public $ServiceCommands; // IN202500EcommerceCrossSellsServiceCommands
}

class IN202500EcommerceCrossSellsServiceCommands {
  public $KeyInventoryID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class IN202500EcommerceUpSells {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $Description; // Field
  public $ServiceCommands; // IN202500EcommerceUpSellsServiceCommands
}

class IN202500EcommerceUpSellsServiceCommands {
  public $KeyInventoryID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class IN202500SpecifyNewID {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $ServiceCommands; // IN202500SpecifyNewIDServiceCommands
}

class IN202500SpecifyNewIDServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class IN202500PrimaryKey {
  public $InventoryID; // Value
}

class IN202500Clear {
}

class IN202500ClearResponse {
}

class IN202500GetProcessStatus {
}

class IN202500GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class IN202500GetSchema {
}

class IN202500GetSchemaResponse {
  public $GetSchemaResult; // IN202500Content
}

class IN202500SetSchema {
  public $schema; // IN202500Content
}

class IN202500SetSchemaResponse {
}

class IN202500Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class IN202500ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class IN202500Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class IN202500ImportResponse {
  public $ImportResult; // IN202500ArrayOfImportResult
}

class IN202500ImportResult {
  public $Processed; // boolean
  public $Error; // string
  public $Keys; // IN202500PrimaryKey
}

class IN202500ArrayOfImportResult {
  public $ImportResult; // IN202500ImportResult
}

class IN202500Submit {
  public $commands; // ArrayOfCommand
}

class IN202500ArrayOfContent {
  public $Content; // IN202500Content
}

class IN202500SubmitResponse {
  public $SubmitResult; // IN202500ArrayOfContent
}

class KN202500Content {
  public $Actions; // KN202500Actions
  public $StockItemSummary; // KN202500StockItemSummary
  public $GeneralSettingsItemDefaults; // KN202500GeneralSettingsItemDefaults
  public $GeneralSettingsWarehouseDefaults; // KN202500GeneralSettingsWarehouseDefaults
  public $GeneralSettingsSubItemsInformation; // KN202500GeneralSettingsSubItemsInformation
  public $GeneralSettingsUnitOfMeasure; // KN202500GeneralSettingsUnitOfMeasure
  public $PriceCostInfoPriceManagement; // KN202500PriceCostInfoPriceManagement
  public $PriceCostInfoStandardCost; // KN202500PriceCostInfoStandardCost
  public $PackagingDimensions; // KN202500PackagingDimensions
  public $PackagingAutomaticPackaging; // KN202500PackagingAutomaticPackaging
  public $Attributes; // KN202500Attributes
  public $ConfigurableProduct; // KN202500ConfigurableProduct
  public $Ecommerce; // KN202500Ecommerce
  public $PackagingAutomaticPackagingBoxes; // KN202500PackagingAutomaticPackagingBoxes
  public $AttributesAttributes; // KN202500AttributesAttributes
  public $AttributesSalesCategories; // KN202500AttributesSalesCategories
  public $SalesCategorySalesCategories; // KN202500SalesCategorySalesCategories
  public $BundleProductMappedItemImage; // KN202500BundleProductMappedItemImage
  public $GroupedProductMappedItemImage; // KN202500GroupedProductMappedItemImage
  public $Subitems; // KN202500Subitems
  public $GroupedProduct; // KN202500GroupedProduct
  public $DownloadableProductMappedInventory; // KN202500DownloadableProductMappedInventory
  public $ConfigurableProductMappedSimpleInventory; // KN202500ConfigurableProductMappedSimpleInventory
  public $ConfigurableProductAttributeList; // KN202500ConfigurableProductAttributeList
  public $ConfigurableProductNewItems; // KN202500ConfigurableProductNewItems
  public $CreateProductLookup; // KN202500CreateProductLookup
  public $BundleProductDefinedOptions; // KN202500BundleProductDefinedOptions
  public $BundleProductMappedInventoriesForSelectedOption; // KN202500BundleProductMappedInventoriesForSelectedOption
  public $BundleProductRulesCreationLookupConditions; // KN202500BundleProductRulesCreationLookupConditions
  public $EcommerceCrossSells; // KN202500EcommerceCrossSells
  public $EcommerceUpSells; // KN202500EcommerceUpSells
  public $GeneralSettingsUnitOfMeasureAdditionalCharges; // KN202500GeneralSettingsUnitOfMeasureAdditionalCharges
  public $PriceCostInfoCostStatisticsItemCostStatistics; // KN202500PriceCostInfoCostStatisticsItemCostStatistics
}

class KN202500Actions {
  public $Save; // Action
  public $Cancel; // Action
  public $Insert; // Action
  public $Delete; // Action
  public $CopyDocumentCopyPaste; // Action
  public $PasteDocumentCopyPaste; // Action
  public $SaveTemplateCopyPaste; // Action
  public $First; // Action
  public $Previous; // Action
  public $Next; // Action
  public $Last; // Action
  public $ConfigureRule; // Action
  public $CreateProduct; // Action
  public $AttachFile; // Action
  public $ViewFile; // Action
  public $ChangeID; // Action
  public $ChangeIDAction; // Action
  public $Inquiry; // Action
  public $AddWarehouseDetail; // Action
  public $UpdateReplenishment; // Action
  public $GenerateSubitems; // Action
  public $GenerateLotSerialNumber; // Action
  public $ViewGroupDetails; // Action
}

class KN202500StockItemSummary {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $ItemStatus; // Field
  public $Description; // Field
  public $CompositeItemType; // Field
  public $ProductWorkgroup; // Field
  public $ProductManager; // Field
  public $ShipItems; // Field
  public $NoteText; // Field
  public $ServiceCommands; // KN202500StockItemSummaryServiceCommands
}

class KN202500StockItemSummaryServiceCommands {
  public $KeyInventoryID; // Key
  public $EveryInventoryID; // EveryValue
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500GeneralSettingsItemDefaults {
  public $DisplayName; // string
  public $ItemClass; // Field
  public $Type; // Field
  public $ValuationMethod; // Field
  public $TaxCategory; // Field
  public $ApplyToChildItems; // Field
  public $ServiceCommands; // KN202500GeneralSettingsItemDefaultsServiceCommands
}

class KN202500GeneralSettingsItemDefaultsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500GeneralSettingsWarehouseDefaults {
  public $DisplayName; // string
  public $DefaultWarehouse; // Field
  public $ApplyToChildItems; // Field
  public $DefaultIssueFrom; // Field
  public $DefaultReceiptTo; // Field
  public $ServiceCommands; // KN202500GeneralSettingsWarehouseDefaultsServiceCommands
}

class KN202500GeneralSettingsWarehouseDefaultsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500GeneralSettingsSubItemsInformation {
  public $DisplayName; // string
  public $DefaultSubitem; // Field
  public $UseOnEntry; // Field
  public $ServiceCommands; // KN202500GeneralSettingsSubItemsInformationServiceCommands
}

class KN202500GeneralSettingsSubItemsInformationServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500GeneralSettingsUnitOfMeasure {
  public $DisplayName; // string
  public $BaseUnit; // Field
  public $ApplyToChildItems; // Field
  public $ServiceCommands; // KN202500GeneralSettingsUnitOfMeasureServiceCommands
}

class KN202500GeneralSettingsUnitOfMeasureServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500PriceCostInfoPriceManagement {
  public $DisplayName; // string
  public $PriceClass; // Field
  public $PriceWorkgroup; // Field
  public $PriceManager; // Field
  public $SubjectToCommission; // Field
  public $MinMarkup; // Field
  public $Markup; // Field
  public $MSRP; // Field
  public $DefaultPrice; // Field
  public $ApplyToChildItems; // Field
  public $ServiceCommands; // KN202500PriceCostInfoPriceManagementServiceCommands
}

class KN202500PriceCostInfoPriceManagementServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500PriceCostInfoStandardCost {
  public $DisplayName; // string
  public $PendingCost; // Field
  public $ApplyToChildItems; // Field
  public $PendingCostDate; // Field
  public $CurrentCost; // Field
  public $EffectiveDate; // Field
  public $LastCost; // Field
  public $ServiceCommands; // KN202500PriceCostInfoStandardCostServiceCommands
}

class KN202500PriceCostInfoStandardCostServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500PackagingDimensions {
  public $DisplayName; // string
  public $Weight; // Field
  public $WeightUOM; // Field
  public $Volume; // Field
  public $VolumeUOM; // Field
  public $ServiceCommands; // KN202500PackagingDimensionsServiceCommands
}

class KN202500PackagingDimensionsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500PackagingAutomaticPackaging {
  public $DisplayName; // string
  public $PackagingOption; // Field
  public $PackSeparately; // Field
  public $ServiceCommands; // KN202500PackagingAutomaticPackagingServiceCommands
}

class KN202500PackagingAutomaticPackagingServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500Attributes {
  public $DisplayName; // string
  public $ImageUrl; // Field
  public $ServiceCommands; // KN202500AttributesServiceCommands
}

class KN202500AttributesServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500ConfigurableProduct {
  public $DisplayName; // string
  public $StockItemMappingType; // Field
  public $ServiceCommands; // KN202500ConfigurableProductServiceCommands
}

class KN202500ConfigurableProductServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500Ecommerce {
  public $DisplayName; // string
  public $Active; // Field
  public $HomePage; // Field
  public $BestSeller; // Field
  public $Visibility; // Field
  public $URLKey; // Field
  public $DescriptionShort; // Field
  public $DescriptionLong; // Field
  public $AlternateSearchKeywords; // Field
  public $MetaTitle; // Field
  public $MetaDescription; // Field
  public $MetaKeywords; // Field
  public $CategoryID; // Field
  public $UpSellID; // Field
  public $CrossSellID; // Field
  public $ServiceCommands; // KN202500EcommerceServiceCommands
}

class KN202500EcommerceServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500PackagingAutomaticPackagingBoxes {
  public $DisplayName; // string
  public $BoxID; // Field
  public $Description; // Field
  public $UOM; // Field
  public $Qty; // Field
  public $MaxWeight; // Field
  public $MaxVolume; // Field
  public $MaxQty; // Field
  public $NoteText; // Field
  public $ServiceCommands; // KN202500PackagingAutomaticPackagingBoxesServiceCommands
}

class KN202500PackagingAutomaticPackagingBoxesServiceCommands {
  public $KeyBoxID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500AttributesAttributes {
  public $DisplayName; // string
  public $Attribute; // Field
  public $Required; // Field
  public $AllowChildToChangeValue; // Field
  public $Value; // Field
  public $ServiceCommands; // KN202500AttributesAttributesServiceCommands
}

class KN202500AttributesAttributesServiceCommands {
  public $KeyAttribute; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500AttributesSalesCategories {
  public $DisplayName; // string
  public $CategoryID; // Field
  public $ServiceCommands; // KN202500AttributesSalesCategoriesServiceCommands
}

class KN202500AttributesSalesCategoriesServiceCommands {
  public $KeyCategoryID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500SalesCategorySalesCategories {
  public $DisplayName; // string
  public $CategoryID; // Field
  public $ServiceCommands; // KN202500SalesCategorySalesCategoriesServiceCommands
}

class KN202500SalesCategorySalesCategoriesServiceCommands {
  public $KeyCategoryID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500BundleProductMappedItemImage {
  public $DisplayName; // string
  public $ImageUrl; // Field
  public $NoteText; // Field
  public $ServiceCommands; // KN202500BundleProductMappedItemImageServiceCommands
}

class KN202500BundleProductMappedItemImageServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500GroupedProductMappedItemImage {
  public $DisplayName; // string
  public $ImageURL; // Field
  public $NoteText; // Field
  public $ServiceCommands; // KN202500GroupedProductMappedItemImageServiceCommands
}

class KN202500GroupedProductMappedItemImageServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500Subitems {
  public $DisplayName; // string
  public $SegmentID; // Field
  public $Value; // Field
  public $ServiceCommands; // KN202500SubitemsServiceCommands
}

class KN202500SubitemsServiceCommands {
  public $KeySegmentID; // Key
  public $KeyValue; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500GroupedProduct {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $ItemClass; // Field
  public $TaxCategory; // Field
  public $BaseUnit; // Field
  public $Quantity; // Field
  public $ServiceCommands; // KN202500GroupedProductServiceCommands
}

class KN202500GroupedProductServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500DownloadableProductMappedInventory {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $ServiceCommands; // KN202500DownloadableProductMappedInventoryServiceCommands
}

class KN202500DownloadableProductMappedInventoryServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500ConfigurableProductMappedSimpleInventory {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $Description; // Field
  public $ServiceCommands; // KN202500ConfigurableProductMappedSimpleInventoryServiceCommands
}

class KN202500ConfigurableProductMappedSimpleInventoryServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500ConfigurableProductAttributeList {
  public $DisplayName; // string
  public $AttributeID; // Field
  public $Description; // Field
  public $Selected; // Field
  public $ServiceCommands; // KN202500ConfigurableProductAttributeListServiceCommands
}

class KN202500ConfigurableProductAttributeListServiceCommands {
  public $KeyAttributeID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500ConfigurableProductNewItems {
  public $DisplayName; // string
  public $CreateStockItem; // Field
  public $InventoryID; // Field
  public $AttributeInfo; // Field
  public $DefaultPrice; // Field
  public $DefaultWarehouse; // Field
  public $TaxCategory; // Field
  public $BaseUnit; // Field
  public $PendingStdCost; // Field
  public $ServiceCommands; // KN202500ConfigurableProductNewItemsServiceCommands
}

class KN202500ConfigurableProductNewItemsServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500CreateProductLookup {
  public $DisplayName; // string
  public $MappedInventoryID; // Field
  public $Description; // Field
  public $ServiceCommands; // KN202500CreateProductLookupServiceCommands
}

class KN202500CreateProductLookupServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN202500BundleProductDefinedOptions {
  public $DisplayName; // string
  public $OptionTitle; // Field
  public $ItemClass; // Field
  public $ControlType; // Field
  public $IsMandatory; // Field
  public $MinRequiredQty; // Field
  public $MaxAllowed; // Field
  public $ServiceCommands; // KN202500BundleProductDefinedOptionsServiceCommands
}

class KN202500BundleProductDefinedOptionsServiceCommands {
  public $KeyOptionTitle; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500BundleProductMappedInventoriesForSelectedOption {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $Quantity; // Field
  public $UserCanDefineQty; // Field
  public $ServiceCommands; // KN202500BundleProductMappedInventoriesForSelectedOptionServiceCommands
}

class KN202500BundleProductMappedInventoriesForSelectedOptionServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500BundleProductRulesCreationLookupConditions {
  public $DisplayName; // string
  public $RuleType; // Field
  public $Option1; // Field
  public $Option1Value; // Field
  public $Option2; // Field
  public $Option2Value; // Field
  public $MinReqQty; // Field
  public $MaxAllowedQty; // Field
  public $ServiceCommands; // KN202500BundleProductRulesCreationLookupConditionsServiceCommands
}

class KN202500BundleProductRulesCreationLookupConditionsServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500EcommerceCrossSells {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $Description; // Field
  public $ServiceCommands; // KN202500EcommerceCrossSellsServiceCommands
}

class KN202500EcommerceCrossSellsServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500EcommerceUpSells {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $Description; // Field
  public $ServiceCommands; // KN202500EcommerceUpSellsServiceCommands
}

class KN202500EcommerceUpSellsServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500GeneralSettingsUnitOfMeasureAdditionalCharges {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $Description; // Field
  public $ServiceCommands; // KN202500GeneralSettingsUnitOfMeasureAdditionalChargesServiceCommands
}

class KN202500GeneralSettingsUnitOfMeasureAdditionalChargesServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500PriceCostInfoCostStatisticsItemCostStatistics {
  public $DisplayName; // string
  public $LastCost; // Field
  public $AverageCost; // Field
  public $MinCost; // Field
  public $MaxCost; // Field
  public $ServiceCommands; // KN202500PriceCostInfoCostStatisticsItemCostStatisticsServiceCommands
}

class KN202500PriceCostInfoCostStatisticsItemCostStatisticsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class KN202500PrimaryKey {
  public $InventoryID; // Value
}

class KN202500Clear {
}

class KN202500ClearResponse {
}

class KN202500GetProcessStatus {
}

class KN202500GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class KN202500GetSchema {
}

class KN202500GetSchemaResponse {
  public $GetSchemaResult; // KN202500Content
}

class KN202500SetSchema {
  public $schema; // KN202500Content
}

class KN202500SetSchemaResponse {
}

class KN202500Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class KN202500ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class KN202500Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class KN202500ImportResponse {
  public $ImportResult; // KN202500ArrayOfImportResult
}

class KN202500ImportResult {
  public $Processed; // boolean
  public $Error; // string
  public $Keys; // KN202500PrimaryKey
}

class KN202500ArrayOfImportResult {
  public $ImportResult; // KN202500ImportResult
}

class KN202500Submit {
  public $commands; // ArrayOfCommand
}

class KN202500ArrayOfContent {
  public $Content; // KN202500Content
}

class KN202500SubmitResponse {
  public $SubmitResult; // KN202500ArrayOfContent
}

class KN505888Content {
  public $Actions; // KN505888Actions
  public $CategoryInfo; // KN505888CategoryInfo
  public $CategoryDetails; // KN505888CategoryDetails
  public $CategoryMembers; // KN505888CategoryMembers
}

class KN505888Actions {
  public $Save; // Action
  public $Cancel; // Action
  public $Insert; // Action
  public $Delete; // Action
  public $CopyDocumentCopyPaste; // Action
  public $PasteDocumentCopyPaste; // Action
  public $SaveTemplateCopyPaste; // Action
  public $First; // Action
  public $Previous; // Action
  public $Next; // Action
  public $Last; // Action
}

class KN505888CategoryInfo {
  public $DisplayName; // string
  public $CategoryID; // Field
  public $ParentCategoryID; // Field
  public $Description; // Field
  public $ResCategoryID; // Field
  public $ServiceCommands; // KN505888CategoryInfoServiceCommands
}

class KN505888CategoryInfoServiceCommands {
  public $EveryResCategoryID; // EveryValue
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN505888CategoryDetails {
  public $DisplayName; // string
  public $Status; // Field
  public $IncludeInNavigationMenu; // Field
  public $ProductSortBy; // Field
  public $URLKey; // Field
  public $DescriptionLong; // Field
  public $MetaTitle; // Field
  public $MetaDescription; // Field
  public $MetaKeywords; // Field
  public $ImageURL; // Field
  public $ServiceCommands; // KN505888CategoryDetailsServiceCommands
}

class KN505888CategoryDetailsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN505888CategoryMembers {
  public $DisplayName; // string
  public $CategoryMembers; // Field
  public $ServiceCommands; // KN505888CategoryMembersServiceCommands
}

class KN505888CategoryMembersServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class KN505888Clear {
}

class KN505888ClearResponse {
}

class KN505888GetProcessStatus {
}

class KN505888GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class KN505888GetSchema {
}

class KN505888GetSchemaResponse {
  public $GetSchemaResult; // KN505888Content
}

class KN505888SetSchema {
  public $schema; // KN505888Content
}

class KN505888SetSchemaResponse {
}

class KN505888Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class KN505888ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class KN505888Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class KN505888ImportResponse {
  public $ImportResult; // KN505888ArrayOfImportResult
}

class KN505888ImportResult {
  public $Processed; // boolean
  public $Error; // string
}

class KN505888ArrayOfImportResult {
  public $ImportResult; // KN505888ImportResult
}

class KN505888Submit {
  public $commands; // ArrayOfCommand
}

class KN505888ArrayOfContent {
  public $Content; // KN505888Content
}

class KN505888SubmitResponse {
  public $SubmitResult; // KN505888ArrayOfContent
}

class SO301000Content {
  public $Actions; // SO301000Actions
  public $OrderSummary; // SO301000OrderSummary
  public $CommissionsDefaultSalesperson; // SO301000CommissionsDefaultSalesperson
  public $FinancialSettingsFinancialInformation; // SO301000FinancialSettingsFinancialInformation
  public $PaymentSettings; // SO301000PaymentSettings
  public $ShippingSettingsShippingInformation; // SO301000ShippingSettingsShippingInformation
  public $Totals; // SO301000Totals
  public $ShopForRatesServicesSettings; // SO301000ShopForRatesServicesSettings
  public $ShopForRatesServicesSettingsIsManualPackage; // SO301000ShopForRatesServicesSettingsIsManualPackage
  public $DocumentDetails; // SO301000DocumentDetails
  public $PurchasingDetailsPurchasingSettings; // SO301000PurchasingDetailsPurchasingSettings
  public $TaxDetails; // SO301000TaxDetails
  public $Shipments; // SO301000Shipments
  public $FinancialSettingsBillToInfo; // SO301000FinancialSettingsBillToInfo
  public $ShippingSettingsShipToInfo; // SO301000ShippingSettingsShipToInfo
  public $FinancialSettingsBillToInfoOverrideContact; // SO301000FinancialSettingsBillToInfoOverrideContact
  public $ShippingSettingsShipToInfoOverrideContact; // SO301000ShippingSettingsShipToInfoOverrideContact
  public $ApprovalDetails; // SO301000ApprovalDetails
  public $OrderSummaryRateSelection; // SO301000OrderSummaryRateSelection
  public $OrderSummaryRateSelectionCurrencyUnitEquivalents; // SO301000OrderSummaryRateSelectionCurrencyUnitEquivalents
  public $Allocations; // SO301000Allocations
  public $DiscountDetails; // SO301000DiscountDetails
  public $CompositeItemsConfigurationLookup; // SO301000CompositeItemsConfigurationLookup
  public $CompositeItemsConfigurationLookupSelected; // SO301000CompositeItemsConfigurationLookupSelected
  public $SpecifyShipmentParameters; // SO301000SpecifyShipmentParameters
  public $AddInvoiceDetailsDocType; // SO301000AddInvoiceDetailsDocType
  public $CopyTo; // SO301000CopyTo
  public $RecalculatePrices; // SO301000RecalculatePrices
  public $AddInvoiceDetails; // SO301000AddInvoiceDetails
  public $PurchasingDetails; // SO301000PurchasingDetails
  public $Commissions; // SO301000Commissions
  public $ShopForRatesPackages; // SO301000ShopForRatesPackages
  public $ShopForRatesCarrierRates; // SO301000ShopForRatesCarrierRates
  public $Payments; // SO301000Payments
  public $PaymentSettingsInputMode; // SO301000PaymentSettingsInputMode
  public $PaymentSettingsCardInfo; // SO301000PaymentSettingsCardInfo
  public $PaymentSettingsDescription; // SO301000PaymentSettingsDescription
  public $InventoryLookupInventory; // SO301000InventoryLookupInventory
  public $InventoryLookup; // SO301000InventoryLookup
  public $CompositeItemsConfigurationLookupType; // SO301000CompositeItemsConfigurationLookupType
  public $CompositeItemsConfigurationLookupType_; // SO301000CompositeItemsConfigurationLookupType_
  public $CompositeItemsConfigurationLookupCompositeNotes; // SO301000CompositeItemsConfigurationLookupCompositeNotes
  public $CompositeItemsConfigurationLookup_; // SO301000CompositeItemsConfigurationLookup_
  public $CompositeItemsConfigurationLookupAvailableOptions; // SO301000CompositeItemsConfigurationLookupAvailableOptions
  public $CompositeItemsConfigurationLookupConfiguredRules; // SO301000CompositeItemsConfigurationLookupConfiguredRules
  public $CompositeItemsConfigurationLookupSelectedItemsThisIncludesMustItems; // SO301000CompositeItemsConfigurationLookupSelectedItemsThisIncludesMustItems
  public $AllocationsUnassignedQty; // SO301000AllocationsUnassignedQty
}

class SO301000Actions {
  public $Save; // Action
  public $Cancel; // Action
  public $Insert; // Action
  public $Delete; // Action
  public $CopyDocumentCopyPaste; // Action
  public $PasteDocumentCopyPaste; // Action
  public $SaveTemplateCopyPaste; // Action
  public $First; // Action
  public $Previous; // Action
  public $Next; // Action
  public $Last; // Action
  public $LSSOLineGenerateLotSerial; // Action
  public $LSSOLineBinLotSerial; // Action
  public $NewTask; // Action
  public $NewEvent; // Action
  public $ViewActivity; // Action
  public $NewMailActivity; // Action
  public $OpenActivityOwner; // Action
  public $ViewAllActivities; // Action
  public $CNewActivity; // Action
  public $ENewActivity; // Action
  public $MNewActivity; // Action
  public $NNewActivity; // Action
  public $PNewActivity; // Action
  public $RNewActivity; // Action
  public $WNewActivity; // Action
  public $POSupplyOK; // Action
  public $Hold; // Action
  public $Cancelled; // Action
  public $CreditHold; // Action
  public $OnDeleteShipmentFlow; // Action
  public $SyncStatusFlow; // Action
  public $OnShipmentFlow; // Action
  public $OnCreateInvoiceFlow; // Action
  public $CreateShipmentAction; // Action
  public $CreateReceiptAction; // Action
  public $OpenOrderAction; // Action
  public $ReOpenOrderAction; // Action
  public $CopyOrderAction; // Action
  public $EmailSalesOrderQuoteAction; // Action
  public $ReleaseFromCreditHoldAction; // Action
  public $PrepareInvoiceAction; // Action
  public $CreatePurchaseOrderAction; // Action
  public $CreateTransferOrderAction; // Action
  public $CancelOrderAction; // Action
  public $PlaceOnBackOrderAction; // Action
  public $ValidateAddressesAction; // Action
  public $RecalculateDiscountsActionAction; // Action
  public $ApproveAction; // Action
  public $RejectAction; // Action
  public $RecalculateAvalaraTaxAction; // Action
  public $Inquiry; // Action
  public $PrintSalesOrderQuoteReport; // Action
  public $EmailSalesOrderQuoteNotification; // Action
  public $PrepareInvoice; // Action
  public $AddInvoice; // Action
  public $AddInvoiceOK; // Action
  public $CheckCopyParams; // Action
  public $ReopenOrder; // Action
  public $CopyOrder; // Action
  public $InventorySummary; // Action
  public $CalculateFreight; // Action
  public $ShopRates; // Action
  public $RefreshRates; // Action
  public $RecalculatePackages; // Action
  public $CreatePayment; // Action
  public $CreatePrepayment; // Action
  public $ViewPayment; // Action
  public $AuthorizeCCPayment; // Action
  public $VoidCCPayment; // Action
  public $CaptureCCPayment; // Action
  public $CreditCCPayment; // Action
  public $ValidateAddresses; // Action
  public $RecalculateDiscountsAction; // Action
  public $RecalcOk; // Action
  public $CreateCCPaymentMethodHF; // Action
  public $SyncCCPaymentMethods; // Action
  public $RecalcAvalara; // Action
  public $AddInvBySite; // Action
  public $AddInvSelBySite; // Action
  public $ConfigurableCancel; // Action
  public $CompositeOK; // Action
  public $CompositeADD; // Action
  public $CompositeStockItem; // Action
  public $ApplyAttributeValues; // Action
  public $CompositeItemRedirect; // Action
  public $AddBundleCancel; // Action
}

class SO301000OrderSummary {
  public $DisplayName; // string
  public $OrderType; // Field
  public $OrderNbr; // Field
  public $Hold; // Field
  public $Status; // Field
  public $DonTApprove; // Field
  public $Approved; // Field
  public $Date; // Field
  public $RequestedOn; // Field
  public $CustomerOrder; // Field
  public $ExternalReference; // Field
  public $Customer; // Field
  public $Location; // Field
  public $Currency; // Field
  public $CuryViewState; // Field
  public $CreditHold; // Field
  public $DestinationWarehouse; // Field
  public $Project; // Field
  public $Description; // Field
  public $OrderedQty; // Field
  public $VATExemptTotal; // Field
  public $VATTaxableTotal; // Field
  public $TaxTotal; // Field
  public $OrderTotal; // Field
  public $ControlTotal; // Field
  public $NoteText; // Field
  public $ServiceCommands; // SO301000OrderSummaryServiceCommands
}

class SO301000OrderSummaryServiceCommands {
  public $KeyOrderType; // Key
  public $EveryOrderType; // EveryValue
  public $KeyOrderNbr; // Key
  public $EveryOrderNbr; // EveryValue
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000CommissionsDefaultSalesperson {
  public $DisplayName; // string
  public $DefaultSalesperson; // Field
  public $ServiceCommands; // SO301000CommissionsDefaultSalespersonServiceCommands
}

class SO301000CommissionsDefaultSalespersonServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000FinancialSettingsFinancialInformation {
  public $DisplayName; // string
  public $Branch; // Field
  public $OverrideTaxZone; // Field
  public $CustomerTaxZone; // Field
  public $EntityUsageType; // Field
  public $BillSeparately; // Field
  public $InvoiceNbr; // Field
  public $InvoiceDate; // Field
  public $Terms; // Field
  public $DueDate; // Field
  public $CashDiscountDate; // Field
  public $PostPeriod; // Field
  public $Owner; // Field
  public $OrigOrderType; // Field
  public $OrigOrderNbr; // Field
  public $ServiceCommands; // SO301000FinancialSettingsFinancialInformationServiceCommands
}

class SO301000FinancialSettingsFinancialInformationServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000PaymentSettings {
  public $DisplayName; // string
  public $PaymentMethod; // Field
  public $CardAccountNo; // Field
  public $CardAccountNoIdentifier; // Field
  public $CashAccount; // Field
  public $PaymentRef; // Field
  public $CCNumber; // Field
  public $NewCard; // Field
  public $ProcessingStatus; // Field
  public $PCResponseReason; // Field
  public $PreAuthNbr; // Field
  public $AuthExpiresOn; // Field
  public $PreAuthorizedAmount; // Field
  public $PaymentsTotal; // Field
  public $UnpaidBalance; // Field
  public $CaptureTranNbr; // Field
  public $OrigPCRefNbr; // Field
  public $ServiceCommands; // SO301000PaymentSettingsServiceCommands
}

class SO301000PaymentSettingsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000ShippingSettingsShippingInformation {
  public $DisplayName; // string
  public $SchedShipment; // Field
  public $ShipSeparately; // Field
  public $ShippingRule; // Field
  public $CancelBy; // Field
  public $Canceled; // Field
  public $PreferredWarehouseID; // Field
  public $ShipVia; // Field
  public $FOBPoint; // Field
  public $Priority; // Field
  public $ShippingTerms; // Field
  public $ShippingZone; // Field
  public $ResidentialDelivery; // Field
  public $SaturdayDelivery; // Field
  public $Insurance; // Field
  public $UseCustomerSAccount; // Field
  public $GroundCollect; // Field
  public $ServiceCommands; // SO301000ShippingSettingsShippingInformationServiceCommands
}

class SO301000ShippingSettingsShippingInformationServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000Totals {
  public $DisplayName; // string
  public $LineTotal; // Field
  public $MiscTotal; // Field
  public $DiscountTotal; // Field
  public $TaxTotal; // Field
  public $OrderWeight; // Field
  public $OrderVolume; // Field
  public $PackageWeight; // Field
  public $FreightCost; // Field
  public $FreightCostIsUpToDate; // Field
  public $Freight; // Field
  public $PremiumFreight; // Field
  public $FreightTaxCategory; // Field
  public $UnshippedQuantity; // Field
  public $UnshippedAmount; // Field
  public $UnbilledQuantity; // Field
  public $UnbilledAmount; // Field
  public $PaymentsTotal; // Field
  public $PreAuthorizedAmount; // Field
  public $UnpaidBalance; // Field
  public $ServiceCommands; // SO301000TotalsServiceCommands
}

class SO301000TotalsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000ShopForRatesServicesSettings {
  public $DisplayName; // string
  public $OrderWeight; // Field
  public $PackageWeight; // Field
  public $ServiceCommands; // SO301000ShopForRatesServicesSettingsServiceCommands
}

class SO301000ShopForRatesServicesSettingsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000ShopForRatesServicesSettingsIsManualPackage {
  public $DisplayName; // string
  public $IsManualPackage; // Field
  public $ServiceCommands; // SO301000ShopForRatesServicesSettingsIsManualPackageServiceCommands
}

class SO301000ShopForRatesServicesSettingsIsManualPackageServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000DocumentDetails {
  public $DisplayName; // string
  public $Branch; // Field
  public $OrderType; // Field
  public $OrderNbr; // Field
  public $LineNbr; // Field
  public $LineType; // Field
  public $InvoiceNbr; // Field
  public $Operation; // Field
  public $InventoryID; // Field
  public $CompositeInventory; // Field
  public $AutoCreateIssue; // Field
  public $FreeItem; // Field
  public $Warehouse; // Field
  public $Location; // Field
  public $LineDescription; // Field
  public $UOM; // Field
  public $Quantity; // Field
  public $QtyOnShipments; // Field
  public $OpenQty; // Field
  public $UnitCost; // Field
  public $UnitPrice; // Field
  public $DiscountPercent; // Field
  public $DiscountAmount; // Field
  public $DiscountCode; // Field
  public $DiscountSequence; // Field
  public $ManualDiscount; // Field
  public $DiscUnitPrice; // Field
  public $AverageCost; // Field
  public $ExtPrice; // Field
  public $TermStartDate; // Field
  public $TermEndDate; // Field
  public $UnbilledAmount; // Field
  public $RequestedOn; // Field
  public $ShipOn; // Field
  public $ShippingRule; // Field
  public $UndershipThreshold; // Field
  public $OvershipThreshold; // Field
  public $Completed; // Field
  public $MarkForPO; // Field
  public $POSource; // Field
  public $ReasonCode; // Field
  public $SalespersonID; // Field
  public $TaxCategory; // Field
  public $Commissionable; // Field
  public $AlternateID; // Field
  public $Account; // Field
  public $Subaccount; // Field
  public $ProjectTask; // Field
  public $UnitPriceForDR; // Field
  public $DiscountPercentForDR; // Field
  public $MasterID; // Field
  public $ParentID; // Field
  public $KNAttributeInfo; // Field
  public $NoteText; // Field
  public $Availability; // Field
  public $ServiceCommands; // SO301000DocumentDetailsServiceCommands
}

class SO301000DocumentDetailsServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000PurchasingDetailsPurchasingSettings {
  public $DisplayName; // string
  public $POSource; // Field
  public $VendorID; // Field
  public $POSiteID; // Field
  public $ServiceCommands; // SO301000PurchasingDetailsPurchasingSettingsServiceCommands
}

class SO301000PurchasingDetailsPurchasingSettingsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000TaxDetails {
  public $DisplayName; // string
  public $TaxID; // Field
  public $TaxRate; // Field
  public $TaxableAmount; // Field
  public $TaxAmount; // Field
  public $TaxType; // Field
  public $PendingVAT; // Field
  public $ReverseVAT; // Field
  public $IncludeInVATExemptTotal; // Field
  public $StatisticalVAT; // Field
  public $ServiceCommands; // SO301000TaxDetailsServiceCommands
}

class SO301000TaxDetailsServiceCommands {
  public $KeyTaxID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000Shipments {
  public $DisplayName; // string
  public $ShipmentNbr; // Field
  public $ShipmentType; // Field
  public $Status; // Field
  public $Operation; // Field
  public $OrderType; // Field
  public $OrderNbr; // Field
  public $ShipmentDate; // Field
  public $ShippedQty; // Field
  public $ShippedWeight; // Field
  public $ShippedVolume; // Field
  public $InvoiceType; // Field
  public $InvoiceNbr; // Field
  public $InventoryDocType; // Field
  public $InventoryRefNbr; // Field
  public $NoteText; // Field
  public $ServiceCommands; // SO301000ShipmentsServiceCommands
}

class SO301000ShipmentsServiceCommands {
  public $KeyShipmentNbr; // Key
  public $KeyShipmentType; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000FinancialSettingsBillToInfo {
  public $DisplayName; // string
  public $OverrideAddress; // Field
  public $Validated; // Field
  public $AddressLine1; // Field
  public $AddressLine2; // Field
  public $City; // Field
  public $Country; // Field
  public $State; // Field
  public $PostalCode; // Field
  public $ServiceCommands; // SO301000FinancialSettingsBillToInfoServiceCommands
}

class SO301000FinancialSettingsBillToInfoServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000ShippingSettingsShipToInfo {
  public $DisplayName; // string
  public $OverrideAddress; // Field
  public $Validated; // Field
  public $AddressLine1; // Field
  public $AddressLine2; // Field
  public $City; // Field
  public $Country; // Field
  public $State; // Field
  public $PostalCode; // Field
  public $ServiceCommands; // SO301000ShippingSettingsShipToInfoServiceCommands
}

class SO301000ShippingSettingsShipToInfoServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000FinancialSettingsBillToInfoOverrideContact {
  public $DisplayName; // string
  public $OverrideContact; // Field
  public $BusinessName; // Field
  public $Attention; // Field
  public $Phone1; // Field
  public $Email; // Field
  public $ServiceCommands; // SO301000FinancialSettingsBillToInfoOverrideContactServiceCommands
}

class SO301000FinancialSettingsBillToInfoOverrideContactServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000ShippingSettingsShipToInfoOverrideContact {
  public $DisplayName; // string
  public $OverrideContact; // Field
  public $BusinessName; // Field
  public $Attention; // Field
  public $Phone1; // Field
  public $Email; // Field
  public $ServiceCommands; // SO301000ShippingSettingsShipToInfoOverrideContactServiceCommands
}

class SO301000ShippingSettingsShipToInfoOverrideContactServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000ApprovalDetails {
  public $DisplayName; // string
  public $Approver; // Field
  public $ApproverName; // Field
  public $ApprovedBy; // Field
  public $ApprovedByName; // Field
  public $Date; // Field
  public $Status; // Field
  public $Workgroup; // Field
  public $NoteText; // Field
  public $ServiceCommands; // SO301000ApprovalDetailsServiceCommands
}

class SO301000ApprovalDetailsServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000OrderSummaryRateSelection {
  public $DisplayName; // string
  public $CurrRateTypeID; // Field
  public $EffectiveDate; // Field
  public $ServiceCommands; // SO301000OrderSummaryRateSelectionServiceCommands
}

class SO301000OrderSummaryRateSelectionServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000OrderSummaryRateSelectionCurrencyUnitEquivalents {
  public $DisplayName; // string
  public $CurrencyID; // Field
  public $BaseCurrencyID; // Field
  public $CurrRate; // Field
  public $ReciprocalRate; // Field
  public $ServiceCommands; // SO301000OrderSummaryRateSelectionCurrencyUnitEquivalentsServiceCommands
}

class SO301000OrderSummaryRateSelectionCurrencyUnitEquivalentsServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000Allocations {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $ShipOn; // Field
  public $Allocated; // Field
  public $AllocWarehouse; // Field
  public $Completed; // Field
  public $Location; // Field
  public $Quantity; // Field
  public $QtyOnShipments; // Field
  public $QtyReceived; // Field
  public $UOM; // Field
  public $RelatedDocument; // Field
  public $ServiceCommands; // SO301000AllocationsServiceCommands
}

class SO301000AllocationsServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000DiscountDetails {
  public $DisplayName; // string
  public $SkipDiscount; // Field
  public $DiscountCode; // Field
  public $SequenceID; // Field
  public $Type; // Field
  public $ManualDiscount; // Field
  public $DiscountableAmt; // Field
  public $DiscountableQty; // Field
  public $DiscountAmt; // Field
  public $Discount; // Field
  public $FreeItem; // Field
  public $FreeItemQty; // Field
  public $ServiceCommands; // SO301000DiscountDetailsServiceCommands
}

class SO301000DiscountDetailsServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000CompositeItemsConfigurationLookup {
  public $DisplayName; // string
  public $Selected; // Field
  public $InventoryID; // Field
  public $Description; // Field
  public $ItemStatus; // Field
  public $DefaultPrice; // Field
  public $Quantity; // Field
  public $ServiceCommands; // SO301000CompositeItemsConfigurationLookupServiceCommands
}

class SO301000CompositeItemsConfigurationLookupServiceCommands {
  public $KeyInventoryID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000CompositeItemsConfigurationLookupSelected {
  public $DisplayName; // string
  public $Selected; // Field
  public $InventoryID; // Field
  public $Description; // Field
  public $ItemStatus; // Field
  public $DefaultPrice; // Field
  public $Quantity; // Field
  public $ServiceCommands; // SO301000CompositeItemsConfigurationLookupSelectedServiceCommands
}

class SO301000CompositeItemsConfigurationLookupSelectedServiceCommands {
  public $KeyInventoryID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000SpecifyShipmentParameters {
  public $DisplayName; // string
  public $ShipmentDate; // Field
  public $WarehouseID; // Field
  public $ServiceCommands; // SO301000SpecifyShipmentParametersServiceCommands
}

class SO301000SpecifyShipmentParametersServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000AddInvoiceDetailsDocType {
  public $DisplayName; // string
  public $DocType; // Field
  public $RefNbr; // Field
  public $ServiceCommands; // SO301000AddInvoiceDetailsDocTypeServiceCommands
}

class SO301000AddInvoiceDetailsDocTypeServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000CopyTo {
  public $DisplayName; // string
  public $OrderType; // Field
  public $OrderNbr; // Field
  public $RecalcUnitPrices; // Field
  public $OverrideManualPrices; // Field
  public $RecalcDiscounts; // Field
  public $OverrideManualDiscounts; // Field
  public $ServiceCommands; // SO301000CopyToServiceCommands
}

class SO301000CopyToServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000RecalculatePrices {
  public $DisplayName; // string
  public $RecalcTarget; // Field
  public $RecalcUnitPrices; // Field
  public $OverrideManualPrices; // Field
  public $RecalcDiscounts; // Field
  public $OverrideManualDiscounts; // Field
  public $ServiceCommands; // SO301000RecalculatePricesServiceCommands
}

class SO301000RecalculatePricesServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000AddInvoiceDetails {
  public $DisplayName; // string
  public $Selected; // Field
  public $InventoryID; // Field
  public $Warehouse; // Field
  public $Location; // Field
  public $UOM; // Field
  public $Quantity; // Field
  public $LineDescription; // Field
  public $ServiceCommands; // SO301000AddInvoiceDetailsServiceCommands
}

class SO301000AddInvoiceDetailsServiceCommands {
  public $ParameterAddInvoiceFilterRefNbr; // Parameter
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000PurchasingDetails {
  public $DisplayName; // string
  public $Selected; // Field
  public $POType; // Field
  public $PONbr; // Field
  public $VendorRef; // Field
  public $LineType; // Field
  public $InventoryID; // Field
  public $Vendor; // Field
  public $VendorName; // Field
  public $Promised; // Field
  public $UOM; // Field
  public $OrderQty; // Field
  public $OpenQty; // Field
  public $LineDescription; // Field
  public $ServiceCommands; // SO301000PurchasingDetailsServiceCommands
}

class SO301000PurchasingDetailsServiceCommands {
  public $KeyPOType; // Key
  public $KeyPONbr; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000Commissions {
  public $DisplayName; // string
  public $SalespersonID; // Field
  public $Commission; // Field
  public $CommissionAmt; // Field
  public $CommissionableAmount; // Field
  public $ServiceCommands; // SO301000CommissionsServiceCommands
}

class SO301000CommissionsServiceCommands {
  public $KeySalespersonID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000ShopForRatesPackages {
  public $DisplayName; // string
  public $BoxID; // Field
  public $Description; // Field
  public $ShipFromWarehouse; // Field
  public $WeightUOM; // Field
  public $Weight; // Field
  public $DeclaredValue; // Field
  public $COD; // Field
  public $ServiceCommands; // SO301000ShopForRatesPackagesServiceCommands
}

class SO301000ShopForRatesPackagesServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000ShopForRatesCarrierRates {
  public $DisplayName; // string
  public $Selected; // Field
  public $Code; // Field
  public $Description; // Field
  public $Amount; // Field
  public $DaysInTransit; // Field
  public $DeliveryDate; // Field
  public $ServiceCommands; // SO301000ShopForRatesCarrierRatesServiceCommands
}

class SO301000ShopForRatesCarrierRatesServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000Payments {
  public $DisplayName; // string
  public $DocType; // Field
  public $ReferenceNbr; // Field
  public $AppliedToOrder; // Field
  public $TransferredToInvoice; // Field
  public $Balance; // Field
  public $Status; // Field
  public $PaymentRef; // Field
  public $PaymentMethod; // Field
  public $CashAccount; // Field
  public $PaymentAmount; // Field
  public $Currency; // Field
  public $NoteText; // Field
  public $ServiceCommands; // SO301000PaymentsServiceCommands
}

class SO301000PaymentsServiceCommands {
  public $KeyDocType; // Key
  public $KeyReferenceNbr; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000PaymentSettingsInputMode {
  public $DisplayName; // string
  public $InputMode; // Field
  public $ServiceCommands; // SO301000PaymentSettingsInputModeServiceCommands
}

class SO301000PaymentSettingsInputModeServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000PaymentSettingsCardInfo {
  public $DisplayName; // string
  public $ProcCenterID; // Field
  public $Identifier; // Field
  public $ServiceCommands; // SO301000PaymentSettingsCardInfoServiceCommands
}

class SO301000PaymentSettingsCardInfoServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000PaymentSettingsDescription {
  public $DisplayName; // string
  public $Description; // Field
  public $Value; // Field
  public $ServiceCommands; // SO301000PaymentSettingsDescriptionServiceCommands
}

class SO301000PaymentSettingsDescriptionServiceCommands {
  public $KeyDescription; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000InventoryLookupInventory {
  public $DisplayName; // string
  public $Inventory; // Field
  public $BarCode; // Field
  public $SiteID; // Field
  public $ItemClassID; // Field
  public $SubItem; // Field
  public $HistoryDate; // Field
  public $Mode; // Field
  public $OnlyAvailable; // Field
  public $ServiceCommands; // SO301000InventoryLookupInventoryServiceCommands
}

class SO301000InventoryLookupInventoryServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000InventoryLookup {
  public $DisplayName; // string
  public $Selected; // Field
  public $QtySelected; // Field
  public $Warehouse; // Field
  public $ItemClassID; // Field
  public $ItemClassDescription; // Field
  public $PriceClassID; // Field
  public $PriceClassDescription; // Field
  public $PreferredVendorID; // Field
  public $PreferredVendorName; // Field
  public $InventoryIDInventoryCD; // Field
  public $Description; // Field
  public $SalesUnit; // Field
  public $QtyAvailable; // Field
  public $QtyOnHand; // Field
  public $QtyLastSales; // Field
  public $Currency; // Field
  public $LastUnitPrice; // Field
  public $LastSalesDate; // Field
  public $AlternateID; // Field
  public $AlternateType; // Field
  public $AlternateDescription; // Field
  public $ServiceCommands; // SO301000InventoryLookupServiceCommands
}

class SO301000InventoryLookupServiceCommands {
  public $KeyWarehouse; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000CompositeItemsConfigurationLookupType {
  public $DisplayName; // string
  public $Type; // Field
  public $CompositeStockID; // Field
  public $CompositeItemOrderQty; // Field
  public $ServiceCommands; // SO301000CompositeItemsConfigurationLookupTypeServiceCommands
}

class SO301000CompositeItemsConfigurationLookupTypeServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000CompositeItemsConfigurationLookupType_ {
  public $DisplayName; // string
  public $Type; // Field
  public $CompositeStockID; // Field
  public $ServiceCommands; // SO301000CompositeItemsConfigurationLookupType_ServiceCommands
}

class SO301000CompositeItemsConfigurationLookupType_ServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000CompositeItemsConfigurationLookupCompositeNotes {
  public $DisplayName; // string
  public $CompositeNotes; // Field
  public $Type; // Field
  public $CompositeStockID; // Field
  public $CompositeItemOrderQty; // Field
  public $ItemOrderTotal; // Field
  public $ItemCount; // Field
  public $ServiceCommands; // SO301000CompositeItemsConfigurationLookupCompositeNotesServiceCommands
}

class SO301000CompositeItemsConfigurationLookupCompositeNotesServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000CompositeItemsConfigurationLookup_ {
  public $DisplayName; // string
  public $AttributeIDDescription; // Field
  public $Value; // Field
  public $ServiceCommands; // SO301000CompositeItemsConfigurationLookup_ServiceCommands
}

class SO301000CompositeItemsConfigurationLookup_ServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000CompositeItemsConfigurationLookupAvailableOptions {
  public $DisplayName; // string
  public $OptionTitle; // Field
  public $SelectProducts; // Field
  public $MinRequiredQty; // Field
  public $MaxAllowed; // Field
  public $ServiceCommands; // SO301000CompositeItemsConfigurationLookupAvailableOptionsServiceCommands
}

class SO301000CompositeItemsConfigurationLookupAvailableOptionsServiceCommands {
  public $KeyOptionTitle; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000CompositeItemsConfigurationLookupConfiguredRules {
  public $DisplayName; // string
  public $ItemID; // Field
  public $RuleType; // Field
  public $ServiceCommands; // SO301000CompositeItemsConfigurationLookupConfiguredRulesServiceCommands
}

class SO301000CompositeItemsConfigurationLookupConfiguredRulesServiceCommands {
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000CompositeItemsConfigurationLookupSelectedItemsThisIncludesMustItems {
  public $DisplayName; // string
  public $InventoryID; // Field
  public $RuleType; // Field
  public $DefaultPrice; // Field
  public $Quantity; // Field
  public $Amount; // Field
  public $ServiceCommands; // SO301000CompositeItemsConfigurationLookupSelectedItemsThisIncludesMustItemsServiceCommands
}

class SO301000CompositeItemsConfigurationLookupSelectedItemsThisIncludesMustItemsServiceCommands {
  public $KeyInventoryID; // Key
  public $NewRow; // NewRow
  public $RowNumber; // RowNumber
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
}

class SO301000AllocationsUnassignedQty {
  public $DisplayName; // string
  public $UnassignedQty; // Field
  public $QuantityToGenerate; // Field
  public $StartLotSerialNumber; // Field
  public $ServiceCommands; // SO301000AllocationsUnassignedQtyServiceCommands
}

class SO301000AllocationsUnassignedQtyServiceCommands {
  public $DeleteRow; // DeleteRow
  public $DialogAnswer; // Answer
  public $Attachment; // Attachment
}

class SO301000PrimaryKey {
  public $OrderType; // Value
  public $OrderNbr; // Value
}

class SO301000Clear {
}

class SO301000ClearResponse {
}

class SO301000GetProcessStatus {
}

class SO301000GetProcessStatusResponse {
  public $GetProcessStatusResult; // ProcessResult
}

class SO301000GetSchema {
}

class SO301000GetSchemaResponse {
  public $GetSchemaResult; // SO301000Content
}

class SO301000SetSchema {
  public $schema; // SO301000Content
}

class SO301000SetSchemaResponse {
}

class SO301000Export {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $topCount; // int
  public $includeHeaders; // boolean
  public $breakOnError; // boolean
}

class SO301000ExportResponse {
  public $ExportResult; // ArrayOfArrayOfString
}

class SO301000Import {
  public $commands; // ArrayOfCommand
  public $filters; // ArrayOfFilter
  public $data; // ArrayOfArrayOfString
  public $includedHeaders; // boolean
  public $breakOnError; // boolean
  public $breakOnIncorrectTarget; // boolean
}

class SO301000ImportResponse {
  public $ImportResult; // SO301000ArrayOfImportResult
}

class SO301000ImportResult {
  public $Processed; // boolean
  public $Error; // string
  public $Keys; // SO301000PrimaryKey
}

class SO301000ArrayOfImportResult {
  public $ImportResult; // SO301000ImportResult
}

class SO301000Submit {
  public $commands; // ArrayOfCommand
}

class SO301000ArrayOfContent {
  public $Content; // SO301000Content
}

class SO301000SubmitResponse {
  public $SubmitResult; // SO301000ArrayOfContent
}


/**
 * Screen class
 * 
 *  
 * 
 * @author    {author}
 * @copyright {copyright}
 * @package   {package}
 */
class Screen extends SoapClient {

  private static $classmap = array(
                                    'ProcessResult' => 'ProcessResult',
                                    'ProcessStatus' => 'ProcessStatus',
                                    'GetScenario' => 'GetScenario',
                                    'GetScenarioResponse' => 'GetScenarioResponse',
                                    'Command' => 'Command',
                                    'ElementDescriptor' => 'ElementDescriptor',
                                    'ElementTypes' => 'ElementTypes',
                                    'SchemaMode' => 'SchemaMode',
                                    'EveryValue' => 'EveryValue',
                                    'Key' => 'Key',
                                    'Action' => 'Action',
                                    'Field' => 'Field',
                                    'Value' => 'Value',
                                    'Answer' => 'Answer',
                                    'RowNumber' => 'RowNumber',
                                    'NewRow' => 'NewRow',
                                    'DeleteRow' => 'DeleteRow',
                                    'Parameter' => 'Parameter',
                                    'Attachment' => 'Attachment',
                                    'Filter' => 'Filter',
                                    'FilterCondition' => 'FilterCondition',
                                    'FilterOperator' => 'FilterOperator',
                                    'Login' => 'Login',
                                    'LoginResult' => 'LoginResult',
                                    'ErrorCode' => 'ErrorCode',
                                    'LoginResponse' => 'LoginResponse',
                                    'Logout' => 'Logout',
                                    'LogoutResponse' => 'LogoutResponse',
                                    'SetBusinessDate' => 'SetBusinessDate',
                                    'SetBusinessDateResponse' => 'SetBusinessDateResponse',
                                    'SetLocaleName' => 'SetLocaleName',
                                    'SetLocaleNameResponse' => 'SetLocaleNameResponse',
                                    'SetSchemaMode' => 'SetSchemaMode',
                                    'SetSchemaModeResponse' => 'SetSchemaModeResponse',
                                    'GI000020Content' => 'GI000020Content',
                                    'GI000020Actions' => 'GI000020Actions',
                                    'GI000020Filter_' => 'GI000020Filter_',
                                    'GI000020Filter_ServiceCommands' => 'GI000020Filter_ServiceCommands',
                                    'GI000020Result' => 'GI000020Result',
                                    'GI000020ResultServiceCommands' => 'GI000020ResultServiceCommands',
                                    'GI000020EnterKeys' => 'GI000020EnterKeys',
                                    'GI000020EnterKeysServiceCommands' => 'GI000020EnterKeysServiceCommands',
                                    'GI000020ValuesForUpdate' => 'GI000020ValuesForUpdate',
                                    'GI000020ValuesForUpdateServiceCommands' => 'GI000020ValuesForUpdateServiceCommands',
                                    'GI000020Clear' => 'GI000020Clear',
                                    'GI000020ClearResponse' => 'GI000020ClearResponse',
                                    'GI000020GetProcessStatus' => 'GI000020GetProcessStatus',
                                    'GI000020GetProcessStatusResponse' => 'GI000020GetProcessStatusResponse',
                                    'GI000020GetSchema' => 'GI000020GetSchema',
                                    'GI000020GetSchemaResponse' => 'GI000020GetSchemaResponse',
                                    'GI000020SetSchema' => 'GI000020SetSchema',
                                    'GI000020SetSchemaResponse' => 'GI000020SetSchemaResponse',
                                    'GI000020Export' => 'GI000020Export',
                                    'GI000020ExportResponse' => 'GI000020ExportResponse',
                                    'GI000020Import' => 'GI000020Import',
                                    'GI000020ImportResponse' => 'GI000020ImportResponse',
                                    'GI000020ImportResult' => 'GI000020ImportResult',
                                    'GI000020ArrayOfImportResult' => 'GI000020ArrayOfImportResult',
                                    'GI000020Submit' => 'GI000020Submit',
                                    'GI000020ArrayOfContent' => 'GI000020ArrayOfContent',
                                    'GI000020SubmitResponse' => 'GI000020SubmitResponse',
                                    'GIKEMS05Content' => 'GIKEMS05Content',
                                    'GIKEMS05Actions' => 'GIKEMS05Actions',
                                    'GIKEMS05Filter_' => 'GIKEMS05Filter_',
                                    'GIKEMS05Filter_ServiceCommands' => 'GIKEMS05Filter_ServiceCommands',
                                    'GIKEMS05Result' => 'GIKEMS05Result',
                                    'GIKEMS05ResultServiceCommands' => 'GIKEMS05ResultServiceCommands',
                                    'GIKEMS05EnterKeys' => 'GIKEMS05EnterKeys',
                                    'GIKEMS05EnterKeysServiceCommands' => 'GIKEMS05EnterKeysServiceCommands',
                                    'GIKEMS05ValuesForUpdate' => 'GIKEMS05ValuesForUpdate',
                                    'GIKEMS05ValuesForUpdateServiceCommands' => 'GIKEMS05ValuesForUpdateServiceCommands',
                                    'GIKEMS05Clear' => 'GIKEMS05Clear',
                                    'GIKEMS05ClearResponse' => 'GIKEMS05ClearResponse',
                                    'GIKEMS05GetProcessStatus' => 'GIKEMS05GetProcessStatus',
                                    'GIKEMS05GetProcessStatusResponse' => 'GIKEMS05GetProcessStatusResponse',
                                    'GIKEMS05GetSchema' => 'GIKEMS05GetSchema',
                                    'GIKEMS05GetSchemaResponse' => 'GIKEMS05GetSchemaResponse',
                                    'GIKEMS05SetSchema' => 'GIKEMS05SetSchema',
                                    'GIKEMS05SetSchemaResponse' => 'GIKEMS05SetSchemaResponse',
                                    'GIKEMS05Export' => 'GIKEMS05Export',
                                    'GIKEMS05ExportResponse' => 'GIKEMS05ExportResponse',
                                    'GIKEMS05Import' => 'GIKEMS05Import',
                                    'GIKEMS05ImportResponse' => 'GIKEMS05ImportResponse',
                                    'GIKEMS05ImportResult' => 'GIKEMS05ImportResult',
                                    'GIKEMS05ArrayOfImportResult' => 'GIKEMS05ArrayOfImportResult',
                                    'GIKEMS05Submit' => 'GIKEMS05Submit',
                                    'GIKEMS05ArrayOfContent' => 'GIKEMS05ArrayOfContent',
                                    'GIKEMS05SubmitResponse' => 'GIKEMS05SubmitResponse',
                                    'GIKEMS06Content' => 'GIKEMS06Content',
                                    'GIKEMS06Actions' => 'GIKEMS06Actions',
                                    'GIKEMS06Filter_' => 'GIKEMS06Filter_',
                                    'GIKEMS06Filter_ServiceCommands' => 'GIKEMS06Filter_ServiceCommands',
                                    'GIKEMS06Result' => 'GIKEMS06Result',
                                    'GIKEMS06ResultServiceCommands' => 'GIKEMS06ResultServiceCommands',
                                    'GIKEMS06EnterKeys' => 'GIKEMS06EnterKeys',
                                    'GIKEMS06EnterKeysServiceCommands' => 'GIKEMS06EnterKeysServiceCommands',
                                    'GIKEMS06ValuesForUpdate' => 'GIKEMS06ValuesForUpdate',
                                    'GIKEMS06ValuesForUpdateServiceCommands' => 'GIKEMS06ValuesForUpdateServiceCommands',
                                    'GIKEMS06Clear' => 'GIKEMS06Clear',
                                    'GIKEMS06ClearResponse' => 'GIKEMS06ClearResponse',
                                    'GIKEMS06GetProcessStatus' => 'GIKEMS06GetProcessStatus',
                                    'GIKEMS06GetProcessStatusResponse' => 'GIKEMS06GetProcessStatusResponse',
                                    'GIKEMS06GetSchema' => 'GIKEMS06GetSchema',
                                    'GIKEMS06GetSchemaResponse' => 'GIKEMS06GetSchemaResponse',
                                    'GIKEMS06SetSchema' => 'GIKEMS06SetSchema',
                                    'GIKEMS06SetSchemaResponse' => 'GIKEMS06SetSchemaResponse',
                                    'GIKEMS06Export' => 'GIKEMS06Export',
                                    'GIKEMS06ExportResponse' => 'GIKEMS06ExportResponse',
                                    'GIKEMS06Import' => 'GIKEMS06Import',
                                    'GIKEMS06ImportResponse' => 'GIKEMS06ImportResponse',
                                    'GIKEMS06ImportResult' => 'GIKEMS06ImportResult',
                                    'GIKEMS06ArrayOfImportResult' => 'GIKEMS06ArrayOfImportResult',
                                    'GIKEMS06Submit' => 'GIKEMS06Submit',
                                    'GIKEMS06ArrayOfContent' => 'GIKEMS06ArrayOfContent',
                                    'GIKEMS06SubmitResponse' => 'GIKEMS06SubmitResponse',
                                    'GIKEMS07Content' => 'GIKEMS07Content',
                                    'GIKEMS07Actions' => 'GIKEMS07Actions',
                                    'GIKEMS07Filter_' => 'GIKEMS07Filter_',
                                    'GIKEMS07Filter_ServiceCommands' => 'GIKEMS07Filter_ServiceCommands',
                                    'GIKEMS07Result' => 'GIKEMS07Result',
                                    'GIKEMS07ResultServiceCommands' => 'GIKEMS07ResultServiceCommands',
                                    'GIKEMS07EnterKeys' => 'GIKEMS07EnterKeys',
                                    'GIKEMS07EnterKeysServiceCommands' => 'GIKEMS07EnterKeysServiceCommands',
                                    'GIKEMS07ValuesForUpdate' => 'GIKEMS07ValuesForUpdate',
                                    'GIKEMS07ValuesForUpdateServiceCommands' => 'GIKEMS07ValuesForUpdateServiceCommands',
                                    'GIKEMS07Clear' => 'GIKEMS07Clear',
                                    'GIKEMS07ClearResponse' => 'GIKEMS07ClearResponse',
                                    'GIKEMS07GetProcessStatus' => 'GIKEMS07GetProcessStatus',
                                    'GIKEMS07GetProcessStatusResponse' => 'GIKEMS07GetProcessStatusResponse',
                                    'GIKEMS07GetSchema' => 'GIKEMS07GetSchema',
                                    'GIKEMS07GetSchemaResponse' => 'GIKEMS07GetSchemaResponse',
                                    'GIKEMS07SetSchema' => 'GIKEMS07SetSchema',
                                    'GIKEMS07SetSchemaResponse' => 'GIKEMS07SetSchemaResponse',
                                    'GIKEMS07Export' => 'GIKEMS07Export',
                                    'GIKEMS07ExportResponse' => 'GIKEMS07ExportResponse',
                                    'GIKEMS07Import' => 'GIKEMS07Import',
                                    'GIKEMS07ImportResponse' => 'GIKEMS07ImportResponse',
                                    'GIKEMS07ImportResult' => 'GIKEMS07ImportResult',
                                    'GIKEMS07ArrayOfImportResult' => 'GIKEMS07ArrayOfImportResult',
                                    'GIKEMS07Submit' => 'GIKEMS07Submit',
                                    'GIKEMS07ArrayOfContent' => 'GIKEMS07ArrayOfContent',
                                    'GIKEMS07SubmitResponse' => 'GIKEMS07SubmitResponse',
                                    'GIKEMS08Content' => 'GIKEMS08Content',
                                    'GIKEMS08Actions' => 'GIKEMS08Actions',
                                    'GIKEMS08Filter_' => 'GIKEMS08Filter_',
                                    'GIKEMS08Filter_ServiceCommands' => 'GIKEMS08Filter_ServiceCommands',
                                    'GIKEMS08Result' => 'GIKEMS08Result',
                                    'GIKEMS08ResultServiceCommands' => 'GIKEMS08ResultServiceCommands',
                                    'GIKEMS08EnterKeys' => 'GIKEMS08EnterKeys',
                                    'GIKEMS08EnterKeysServiceCommands' => 'GIKEMS08EnterKeysServiceCommands',
                                    'GIKEMS08ValuesForUpdate' => 'GIKEMS08ValuesForUpdate',
                                    'GIKEMS08ValuesForUpdateServiceCommands' => 'GIKEMS08ValuesForUpdateServiceCommands',
                                    'GIKEMS08Clear' => 'GIKEMS08Clear',
                                    'GIKEMS08ClearResponse' => 'GIKEMS08ClearResponse',
                                    'GIKEMS08GetProcessStatus' => 'GIKEMS08GetProcessStatus',
                                    'GIKEMS08GetProcessStatusResponse' => 'GIKEMS08GetProcessStatusResponse',
                                    'GIKEMS08GetSchema' => 'GIKEMS08GetSchema',
                                    'GIKEMS08GetSchemaResponse' => 'GIKEMS08GetSchemaResponse',
                                    'GIKEMS08SetSchema' => 'GIKEMS08SetSchema',
                                    'GIKEMS08SetSchemaResponse' => 'GIKEMS08SetSchemaResponse',
                                    'GIKEMS08Export' => 'GIKEMS08Export',
                                    'GIKEMS08ExportResponse' => 'GIKEMS08ExportResponse',
                                    'GIKEMS08Import' => 'GIKEMS08Import',
                                    'GIKEMS08ImportResponse' => 'GIKEMS08ImportResponse',
                                    'GIKEMS08ImportResult' => 'GIKEMS08ImportResult',
                                    'GIKEMS08ArrayOfImportResult' => 'GIKEMS08ArrayOfImportResult',
                                    'GIKEMS08Submit' => 'GIKEMS08Submit',
                                    'GIKEMS08ArrayOfContent' => 'GIKEMS08ArrayOfContent',
                                    'GIKEMS08SubmitResponse' => 'GIKEMS08SubmitResponse',
                                    'GIKEMS10Content' => 'GIKEMS10Content',
                                    'GIKEMS10Actions' => 'GIKEMS10Actions',
                                    'GIKEMS10Filter_' => 'GIKEMS10Filter_',
                                    'GIKEMS10Filter_ServiceCommands' => 'GIKEMS10Filter_ServiceCommands',
                                    'GIKEMS10Result' => 'GIKEMS10Result',
                                    'GIKEMS10ResultServiceCommands' => 'GIKEMS10ResultServiceCommands',
                                    'GIKEMS10EnterKeys' => 'GIKEMS10EnterKeys',
                                    'GIKEMS10EnterKeysServiceCommands' => 'GIKEMS10EnterKeysServiceCommands',
                                    'GIKEMS10ValuesForUpdate' => 'GIKEMS10ValuesForUpdate',
                                    'GIKEMS10ValuesForUpdateServiceCommands' => 'GIKEMS10ValuesForUpdateServiceCommands',
                                    'GIKEMS10Clear' => 'GIKEMS10Clear',
                                    'GIKEMS10ClearResponse' => 'GIKEMS10ClearResponse',
                                    'GIKEMS10GetProcessStatus' => 'GIKEMS10GetProcessStatus',
                                    'GIKEMS10GetProcessStatusResponse' => 'GIKEMS10GetProcessStatusResponse',
                                    'GIKEMS10GetSchema' => 'GIKEMS10GetSchema',
                                    'GIKEMS10GetSchemaResponse' => 'GIKEMS10GetSchemaResponse',
                                    'GIKEMS10SetSchema' => 'GIKEMS10SetSchema',
                                    'GIKEMS10SetSchemaResponse' => 'GIKEMS10SetSchemaResponse',
                                    'GIKEMS10Export' => 'GIKEMS10Export',
                                    'GIKEMS10ExportResponse' => 'GIKEMS10ExportResponse',
                                    'GIKEMS10Import' => 'GIKEMS10Import',
                                    'GIKEMS10ImportResponse' => 'GIKEMS10ImportResponse',
                                    'GIKEMS10ImportResult' => 'GIKEMS10ImportResult',
                                    'GIKEMS10ArrayOfImportResult' => 'GIKEMS10ArrayOfImportResult',
                                    'GIKEMS10Submit' => 'GIKEMS10Submit',
                                    'GIKEMS10ArrayOfContent' => 'GIKEMS10ArrayOfContent',
                                    'GIKEMS10SubmitResponse' => 'GIKEMS10SubmitResponse',
                                    'GIKEMS12Content' => 'GIKEMS12Content',
                                    'GIKEMS12Actions' => 'GIKEMS12Actions',
                                    'GIKEMS12Filter_' => 'GIKEMS12Filter_',
                                    'GIKEMS12Filter_ServiceCommands' => 'GIKEMS12Filter_ServiceCommands',
                                    'GIKEMS12Result' => 'GIKEMS12Result',
                                    'GIKEMS12ResultServiceCommands' => 'GIKEMS12ResultServiceCommands',
                                    'GIKEMS12EnterKeys' => 'GIKEMS12EnterKeys',
                                    'GIKEMS12EnterKeysServiceCommands' => 'GIKEMS12EnterKeysServiceCommands',
                                    'GIKEMS12ValuesForUpdate' => 'GIKEMS12ValuesForUpdate',
                                    'GIKEMS12ValuesForUpdateServiceCommands' => 'GIKEMS12ValuesForUpdateServiceCommands',
                                    'GIKEMS12Clear' => 'GIKEMS12Clear',
                                    'GIKEMS12ClearResponse' => 'GIKEMS12ClearResponse',
                                    'GIKEMS12GetProcessStatus' => 'GIKEMS12GetProcessStatus',
                                    'GIKEMS12GetProcessStatusResponse' => 'GIKEMS12GetProcessStatusResponse',
                                    'GIKEMS12GetSchema' => 'GIKEMS12GetSchema',
                                    'GIKEMS12GetSchemaResponse' => 'GIKEMS12GetSchemaResponse',
                                    'GIKEMS12SetSchema' => 'GIKEMS12SetSchema',
                                    'GIKEMS12SetSchemaResponse' => 'GIKEMS12SetSchemaResponse',
                                    'GIKEMS12Export' => 'GIKEMS12Export',
                                    'GIKEMS12ExportResponse' => 'GIKEMS12ExportResponse',
                                    'GIKEMS12Import' => 'GIKEMS12Import',
                                    'GIKEMS12ImportResponse' => 'GIKEMS12ImportResponse',
                                    'GIKEMS12ImportResult' => 'GIKEMS12ImportResult',
                                    'GIKEMS12ArrayOfImportResult' => 'GIKEMS12ArrayOfImportResult',
                                    'GIKEMS12Submit' => 'GIKEMS12Submit',
                                    'GIKEMS12ArrayOfContent' => 'GIKEMS12ArrayOfContent',
                                    'GIKEMS12SubmitResponse' => 'GIKEMS12SubmitResponse',
                                    'GIKEMS13Content' => 'GIKEMS13Content',
                                    'GIKEMS13Actions' => 'GIKEMS13Actions',
                                    'GIKEMS13Filter_' => 'GIKEMS13Filter_',
                                    'GIKEMS13Filter_ServiceCommands' => 'GIKEMS13Filter_ServiceCommands',
                                    'GIKEMS13Result' => 'GIKEMS13Result',
                                    'GIKEMS13ResultServiceCommands' => 'GIKEMS13ResultServiceCommands',
                                    'GIKEMS13EnterKeys' => 'GIKEMS13EnterKeys',
                                    'GIKEMS13EnterKeysServiceCommands' => 'GIKEMS13EnterKeysServiceCommands',
                                    'GIKEMS13ValuesForUpdate' => 'GIKEMS13ValuesForUpdate',
                                    'GIKEMS13ValuesForUpdateServiceCommands' => 'GIKEMS13ValuesForUpdateServiceCommands',
                                    'GIKEMS13Clear' => 'GIKEMS13Clear',
                                    'GIKEMS13ClearResponse' => 'GIKEMS13ClearResponse',
                                    'GIKEMS13GetProcessStatus' => 'GIKEMS13GetProcessStatus',
                                    'GIKEMS13GetProcessStatusResponse' => 'GIKEMS13GetProcessStatusResponse',
                                    'GIKEMS13GetSchema' => 'GIKEMS13GetSchema',
                                    'GIKEMS13GetSchemaResponse' => 'GIKEMS13GetSchemaResponse',
                                    'GIKEMS13SetSchema' => 'GIKEMS13SetSchema',
                                    'GIKEMS13SetSchemaResponse' => 'GIKEMS13SetSchemaResponse',
                                    'GIKEMS13Export' => 'GIKEMS13Export',
                                    'GIKEMS13ExportResponse' => 'GIKEMS13ExportResponse',
                                    'GIKEMS13Import' => 'GIKEMS13Import',
                                    'GIKEMS13ImportResponse' => 'GIKEMS13ImportResponse',
                                    'GIKEMS13ImportResult' => 'GIKEMS13ImportResult',
                                    'GIKEMS13ArrayOfImportResult' => 'GIKEMS13ArrayOfImportResult',
                                    'GIKEMS13Submit' => 'GIKEMS13Submit',
                                    'GIKEMS13ArrayOfContent' => 'GIKEMS13ArrayOfContent',
                                    'GIKEMS13SubmitResponse' => 'GIKEMS13SubmitResponse',
                                    'GIKEMS16Content' => 'GIKEMS16Content',
                                    'GIKEMS16Actions' => 'GIKEMS16Actions',
                                    'GIKEMS16Filter_' => 'GIKEMS16Filter_',
                                    'GIKEMS16Filter_ServiceCommands' => 'GIKEMS16Filter_ServiceCommands',
                                    'GIKEMS16Result' => 'GIKEMS16Result',
                                    'GIKEMS16ResultServiceCommands' => 'GIKEMS16ResultServiceCommands',
                                    'GIKEMS16EnterKeys' => 'GIKEMS16EnterKeys',
                                    'GIKEMS16EnterKeysServiceCommands' => 'GIKEMS16EnterKeysServiceCommands',
                                    'GIKEMS16ValuesForUpdate' => 'GIKEMS16ValuesForUpdate',
                                    'GIKEMS16ValuesForUpdateServiceCommands' => 'GIKEMS16ValuesForUpdateServiceCommands',
                                    'GIKEMS16Clear' => 'GIKEMS16Clear',
                                    'GIKEMS16ClearResponse' => 'GIKEMS16ClearResponse',
                                    'GIKEMS16GetProcessStatus' => 'GIKEMS16GetProcessStatus',
                                    'GIKEMS16GetProcessStatusResponse' => 'GIKEMS16GetProcessStatusResponse',
                                    'GIKEMS16GetSchema' => 'GIKEMS16GetSchema',
                                    'GIKEMS16GetSchemaResponse' => 'GIKEMS16GetSchemaResponse',
                                    'GIKEMS16SetSchema' => 'GIKEMS16SetSchema',
                                    'GIKEMS16SetSchemaResponse' => 'GIKEMS16SetSchemaResponse',
                                    'GIKEMS16Export' => 'GIKEMS16Export',
                                    'GIKEMS16ExportResponse' => 'GIKEMS16ExportResponse',
                                    'GIKEMS16Import' => 'GIKEMS16Import',
                                    'GIKEMS16ImportResponse' => 'GIKEMS16ImportResponse',
                                    'GIKEMS16ImportResult' => 'GIKEMS16ImportResult',
                                    'GIKEMS16ArrayOfImportResult' => 'GIKEMS16ArrayOfImportResult',
                                    'GIKEMS16Submit' => 'GIKEMS16Submit',
                                    'GIKEMS16ArrayOfContent' => 'GIKEMS16ArrayOfContent',
                                    'GIKEMS16SubmitResponse' => 'GIKEMS16SubmitResponse',
                                    'GIKEMS18Content' => 'GIKEMS18Content',
                                    'GIKEMS18Actions' => 'GIKEMS18Actions',
                                    'GIKEMS18Filter_' => 'GIKEMS18Filter_',
                                    'GIKEMS18Filter_ServiceCommands' => 'GIKEMS18Filter_ServiceCommands',
                                    'GIKEMS18Result' => 'GIKEMS18Result',
                                    'GIKEMS18ResultServiceCommands' => 'GIKEMS18ResultServiceCommands',
                                    'GIKEMS18EnterKeys' => 'GIKEMS18EnterKeys',
                                    'GIKEMS18EnterKeysServiceCommands' => 'GIKEMS18EnterKeysServiceCommands',
                                    'GIKEMS18ValuesForUpdate' => 'GIKEMS18ValuesForUpdate',
                                    'GIKEMS18ValuesForUpdateServiceCommands' => 'GIKEMS18ValuesForUpdateServiceCommands',
                                    'GIKEMS18Clear' => 'GIKEMS18Clear',
                                    'GIKEMS18ClearResponse' => 'GIKEMS18ClearResponse',
                                    'GIKEMS18GetProcessStatus' => 'GIKEMS18GetProcessStatus',
                                    'GIKEMS18GetProcessStatusResponse' => 'GIKEMS18GetProcessStatusResponse',
                                    'GIKEMS18GetSchema' => 'GIKEMS18GetSchema',
                                    'GIKEMS18GetSchemaResponse' => 'GIKEMS18GetSchemaResponse',
                                    'GIKEMS18SetSchema' => 'GIKEMS18SetSchema',
                                    'GIKEMS18SetSchemaResponse' => 'GIKEMS18SetSchemaResponse',
                                    'GIKEMS18Export' => 'GIKEMS18Export',
                                    'GIKEMS18ExportResponse' => 'GIKEMS18ExportResponse',
                                    'GIKEMS18Import' => 'GIKEMS18Import',
                                    'GIKEMS18ImportResponse' => 'GIKEMS18ImportResponse',
                                    'GIKEMS18ImportResult' => 'GIKEMS18ImportResult',
                                    'GIKEMS18ArrayOfImportResult' => 'GIKEMS18ArrayOfImportResult',
                                    'GIKEMS18Submit' => 'GIKEMS18Submit',
                                    'GIKEMS18ArrayOfContent' => 'GIKEMS18ArrayOfContent',
                                    'GIKEMS18SubmitResponse' => 'GIKEMS18SubmitResponse',
                                    'GIKEMS19Content' => 'GIKEMS19Content',
                                    'GIKEMS19Actions' => 'GIKEMS19Actions',
                                    'GIKEMS19Result' => 'GIKEMS19Result',
                                    'GIKEMS19ResultServiceCommands' => 'GIKEMS19ResultServiceCommands',
                                    'GIKEMS19EnterKeys' => 'GIKEMS19EnterKeys',
                                    'GIKEMS19EnterKeysServiceCommands' => 'GIKEMS19EnterKeysServiceCommands',
                                    'GIKEMS19ValuesForUpdate' => 'GIKEMS19ValuesForUpdate',
                                    'GIKEMS19ValuesForUpdateServiceCommands' => 'GIKEMS19ValuesForUpdateServiceCommands',
                                    'GIKEMS19PrimaryKey' => 'GIKEMS19PrimaryKey',
                                    'GIKEMS19Clear' => 'GIKEMS19Clear',
                                    'GIKEMS19ClearResponse' => 'GIKEMS19ClearResponse',
                                    'GIKEMS19GetProcessStatus' => 'GIKEMS19GetProcessStatus',
                                    'GIKEMS19GetProcessStatusResponse' => 'GIKEMS19GetProcessStatusResponse',
                                    'GIKEMS19GetSchema' => 'GIKEMS19GetSchema',
                                    'GIKEMS19GetSchemaResponse' => 'GIKEMS19GetSchemaResponse',
                                    'GIKEMS19SetSchema' => 'GIKEMS19SetSchema',
                                    'GIKEMS19SetSchemaResponse' => 'GIKEMS19SetSchemaResponse',
                                    'GIKEMS19Export' => 'GIKEMS19Export',
                                    'GIKEMS19ExportResponse' => 'GIKEMS19ExportResponse',
                                    'GIKEMS19Import' => 'GIKEMS19Import',
                                    'GIKEMS19ImportResponse' => 'GIKEMS19ImportResponse',
                                    'GIKEMS19ImportResult' => 'GIKEMS19ImportResult',
                                    'GIKEMS19ArrayOfImportResult' => 'GIKEMS19ArrayOfImportResult',
                                    'GIKEMS19Submit' => 'GIKEMS19Submit',
                                    'GIKEMS19ArrayOfContent' => 'GIKEMS19ArrayOfContent',
                                    'GIKEMS19SubmitResponse' => 'GIKEMS19SubmitResponse',
                                    'GIKEMS21Content' => 'GIKEMS21Content',
                                    'GIKEMS21Actions' => 'GIKEMS21Actions',
                                    'GIKEMS21Filter_' => 'GIKEMS21Filter_',
                                    'GIKEMS21Filter_ServiceCommands' => 'GIKEMS21Filter_ServiceCommands',
                                    'GIKEMS21Result' => 'GIKEMS21Result',
                                    'GIKEMS21ResultServiceCommands' => 'GIKEMS21ResultServiceCommands',
                                    'GIKEMS21EnterKeys' => 'GIKEMS21EnterKeys',
                                    'GIKEMS21EnterKeysServiceCommands' => 'GIKEMS21EnterKeysServiceCommands',
                                    'GIKEMS21ValuesForUpdate' => 'GIKEMS21ValuesForUpdate',
                                    'GIKEMS21ValuesForUpdateServiceCommands' => 'GIKEMS21ValuesForUpdateServiceCommands',
                                    'GIKEMS21Clear' => 'GIKEMS21Clear',
                                    'GIKEMS21ClearResponse' => 'GIKEMS21ClearResponse',
                                    'GIKEMS21GetProcessStatus' => 'GIKEMS21GetProcessStatus',
                                    'GIKEMS21GetProcessStatusResponse' => 'GIKEMS21GetProcessStatusResponse',
                                    'GIKEMS21GetSchema' => 'GIKEMS21GetSchema',
                                    'GIKEMS21GetSchemaResponse' => 'GIKEMS21GetSchemaResponse',
                                    'GIKEMS21SetSchema' => 'GIKEMS21SetSchema',
                                    'GIKEMS21SetSchemaResponse' => 'GIKEMS21SetSchemaResponse',
                                    'GIKEMS21Export' => 'GIKEMS21Export',
                                    'GIKEMS21ExportResponse' => 'GIKEMS21ExportResponse',
                                    'GIKEMS21Import' => 'GIKEMS21Import',
                                    'GIKEMS21ImportResponse' => 'GIKEMS21ImportResponse',
                                    'GIKEMS21ImportResult' => 'GIKEMS21ImportResult',
                                    'GIKEMS21ArrayOfImportResult' => 'GIKEMS21ArrayOfImportResult',
                                    'GIKEMS21Submit' => 'GIKEMS21Submit',
                                    'GIKEMS21ArrayOfContent' => 'GIKEMS21ArrayOfContent',
                                    'GIKEMS21SubmitResponse' => 'GIKEMS21SubmitResponse',
                                    'GIKEMS22Content' => 'GIKEMS22Content',
                                    'GIKEMS22Actions' => 'GIKEMS22Actions',
                                    'GIKEMS22Filter_' => 'GIKEMS22Filter_',
                                    'GIKEMS22Filter_ServiceCommands' => 'GIKEMS22Filter_ServiceCommands',
                                    'GIKEMS22Result' => 'GIKEMS22Result',
                                    'GIKEMS22ResultServiceCommands' => 'GIKEMS22ResultServiceCommands',
                                    'GIKEMS22EnterKeys' => 'GIKEMS22EnterKeys',
                                    'GIKEMS22EnterKeysServiceCommands' => 'GIKEMS22EnterKeysServiceCommands',
                                    'GIKEMS22ValuesForUpdate' => 'GIKEMS22ValuesForUpdate',
                                    'GIKEMS22ValuesForUpdateServiceCommands' => 'GIKEMS22ValuesForUpdateServiceCommands',
                                    'GIKEMS22Clear' => 'GIKEMS22Clear',
                                    'GIKEMS22ClearResponse' => 'GIKEMS22ClearResponse',
                                    'GIKEMS22GetProcessStatus' => 'GIKEMS22GetProcessStatus',
                                    'GIKEMS22GetProcessStatusResponse' => 'GIKEMS22GetProcessStatusResponse',
                                    'GIKEMS22GetSchema' => 'GIKEMS22GetSchema',
                                    'GIKEMS22GetSchemaResponse' => 'GIKEMS22GetSchemaResponse',
                                    'GIKEMS22SetSchema' => 'GIKEMS22SetSchema',
                                    'GIKEMS22SetSchemaResponse' => 'GIKEMS22SetSchemaResponse',
                                    'GIKEMS22Export' => 'GIKEMS22Export',
                                    'GIKEMS22ExportResponse' => 'GIKEMS22ExportResponse',
                                    'GIKEMS22Import' => 'GIKEMS22Import',
                                    'GIKEMS22ImportResponse' => 'GIKEMS22ImportResponse',
                                    'GIKEMS22ImportResult' => 'GIKEMS22ImportResult',
                                    'GIKEMS22ArrayOfImportResult' => 'GIKEMS22ArrayOfImportResult',
                                    'GIKEMS22Submit' => 'GIKEMS22Submit',
                                    'GIKEMS22ArrayOfContent' => 'GIKEMS22ArrayOfContent',
                                    'GIKEMS22SubmitResponse' => 'GIKEMS22SubmitResponse',
                                    'IN202500Content' => 'IN202500Content',
                                    'IN202500Actions' => 'IN202500Actions',
                                    'IN202500StockItemSummary' => 'IN202500StockItemSummary',
                                    'IN202500StockItemSummaryServiceCommands' => 'IN202500StockItemSummaryServiceCommands',
                                    'IN202500GeneralSettingsItemDefaults' => 'IN202500GeneralSettingsItemDefaults',
                                    'IN202500GeneralSettingsItemDefaultsServiceCommands' => 'IN202500GeneralSettingsItemDefaultsServiceCommands',
                                    'IN202500GeneralSettingsWarehouseDefaults' => 'IN202500GeneralSettingsWarehouseDefaults',
                                    'IN202500GeneralSettingsWarehouseDefaultsServiceCommands' => 'IN202500GeneralSettingsWarehouseDefaultsServiceCommands',
                                    'IN202500GeneralSettingsUnitOfMeasureBaseUnit' => 'IN202500GeneralSettingsUnitOfMeasureBaseUnit',
                                    'IN202500GeneralSettingsUnitOfMeasureBaseUnitServiceCommands' => 'IN202500GeneralSettingsUnitOfMeasureBaseUnitServiceCommands',
                                    'IN202500GeneralSettingsPhysicalInventory' => 'IN202500GeneralSettingsPhysicalInventory',
                                    'IN202500GeneralSettingsPhysicalInventoryServiceCommands' => 'IN202500GeneralSettingsPhysicalInventoryServiceCommands',
                                    'IN202500PriceCostInfoPriceManagement' => 'IN202500PriceCostInfoPriceManagement',
                                    'IN202500PriceCostInfoPriceManagementServiceCommands' => 'IN202500PriceCostInfoPriceManagementServiceCommands',
                                    'IN202500PriceCostInfoStandardCost' => 'IN202500PriceCostInfoStandardCost',
                                    'IN202500PriceCostInfoStandardCostServiceCommands' => 'IN202500PriceCostInfoStandardCostServiceCommands',
                                    'IN202500Ecommerce' => 'IN202500Ecommerce',
                                    'IN202500EcommerceServiceCommands' => 'IN202500EcommerceServiceCommands',
                                    'IN202500Attributes' => 'IN202500Attributes',
                                    'IN202500AttributesServiceCommands' => 'IN202500AttributesServiceCommands',
                                    'IN202500PackagingDimensions' => 'IN202500PackagingDimensions',
                                    'IN202500PackagingDimensionsServiceCommands' => 'IN202500PackagingDimensionsServiceCommands',
                                    'IN202500PackagingAutomaticPackaging' => 'IN202500PackagingAutomaticPackaging',
                                    'IN202500PackagingAutomaticPackagingServiceCommands' => 'IN202500PackagingAutomaticPackagingServiceCommands',
                                    'IN202500GLAccounts' => 'IN202500GLAccounts',
                                    'IN202500GLAccountsServiceCommands' => 'IN202500GLAccountsServiceCommands',
                                    'IN202500Description' => 'IN202500Description',
                                    'IN202500DescriptionServiceCommands' => 'IN202500DescriptionServiceCommands',
                                    'IN202500Subitems' => 'IN202500Subitems',
                                    'IN202500SubitemsServiceCommands' => 'IN202500SubitemsServiceCommands',
                                    'IN202500PriceCostInfoCostStatistics' => 'IN202500PriceCostInfoCostStatistics',
                                    'IN202500PriceCostInfoCostStatisticsServiceCommands' => 'IN202500PriceCostInfoCostStatisticsServiceCommands',
                                    'IN202500GeneralSettingsUnitOfMeasure' => 'IN202500GeneralSettingsUnitOfMeasure',
                                    'IN202500GeneralSettingsUnitOfMeasureServiceCommands' => 'IN202500GeneralSettingsUnitOfMeasureServiceCommands',
                                    'IN202500WarehouseDetails' => 'IN202500WarehouseDetails',
                                    'IN202500WarehouseDetailsServiceCommands' => 'IN202500WarehouseDetailsServiceCommands',
                                    'IN202500CrossReference' => 'IN202500CrossReference',
                                    'IN202500CrossReferenceServiceCommands' => 'IN202500CrossReferenceServiceCommands',
                                    'IN202500ReplenishmentInfoReplenishmentParameters' => 'IN202500ReplenishmentInfoReplenishmentParameters',
                                    'IN202500ReplenishmentInfoReplenishmentParametersServiceCommands' => 'IN202500ReplenishmentInfoReplenishmentParametersServiceCommands',
                                    'IN202500ReplenishmentInfoSubitemReplenishmentParameters' => 'IN202500ReplenishmentInfoSubitemReplenishmentParameters',
                                    'IN202500ReplenishmentInfoSubitemReplenishmentParametersServiceCommands' => 'IN202500ReplenishmentInfoSubitemReplenishmentParametersServiceCommands',
                                    'IN202500VendorDetails' => 'IN202500VendorDetails',
                                    'IN202500VendorDetailsServiceCommands' => 'IN202500VendorDetailsServiceCommands',
                                    'IN202500PackagingAutomaticPackagingBoxes' => 'IN202500PackagingAutomaticPackagingBoxes',
                                    'IN202500PackagingAutomaticPackagingBoxesServiceCommands' => 'IN202500PackagingAutomaticPackagingBoxesServiceCommands',
                                    'IN202500AttributesAttributes' => 'IN202500AttributesAttributes',
                                    'IN202500AttributesAttributesServiceCommands' => 'IN202500AttributesAttributesServiceCommands',
                                    'IN202500AttributesSalesCategories' => 'IN202500AttributesSalesCategories',
                                    'IN202500AttributesSalesCategoriesServiceCommands' => 'IN202500AttributesSalesCategoriesServiceCommands',
                                    'IN202500RestrictionGroups' => 'IN202500RestrictionGroups',
                                    'IN202500RestrictionGroupsServiceCommands' => 'IN202500RestrictionGroupsServiceCommands',
                                    'IN202500EcommerceCrossSells' => 'IN202500EcommerceCrossSells',
                                    'IN202500EcommerceCrossSellsServiceCommands' => 'IN202500EcommerceCrossSellsServiceCommands',
                                    'IN202500EcommerceUpSells' => 'IN202500EcommerceUpSells',
                                    'IN202500EcommerceUpSellsServiceCommands' => 'IN202500EcommerceUpSellsServiceCommands',
                                    'IN202500SpecifyNewID' => 'IN202500SpecifyNewID',
                                    'IN202500SpecifyNewIDServiceCommands' => 'IN202500SpecifyNewIDServiceCommands',
                                    'IN202500PrimaryKey' => 'IN202500PrimaryKey',
                                    'IN202500Clear' => 'IN202500Clear',
                                    'IN202500ClearResponse' => 'IN202500ClearResponse',
                                    'IN202500GetProcessStatus' => 'IN202500GetProcessStatus',
                                    'IN202500GetProcessStatusResponse' => 'IN202500GetProcessStatusResponse',
                                    'IN202500GetSchema' => 'IN202500GetSchema',
                                    'IN202500GetSchemaResponse' => 'IN202500GetSchemaResponse',
                                    'IN202500SetSchema' => 'IN202500SetSchema',
                                    'IN202500SetSchemaResponse' => 'IN202500SetSchemaResponse',
                                    'IN202500Export' => 'IN202500Export',
                                    'IN202500ExportResponse' => 'IN202500ExportResponse',
                                    'IN202500Import' => 'IN202500Import',
                                    'IN202500ImportResponse' => 'IN202500ImportResponse',
                                    'IN202500ImportResult' => 'IN202500ImportResult',
                                    'IN202500ArrayOfImportResult' => 'IN202500ArrayOfImportResult',
                                    'IN202500Submit' => 'IN202500Submit',
                                    'IN202500ArrayOfContent' => 'IN202500ArrayOfContent',
                                    'IN202500SubmitResponse' => 'IN202500SubmitResponse',
                                    'KN202500Content' => 'KN202500Content',
                                    'KN202500Actions' => 'KN202500Actions',
                                    'KN202500StockItemSummary' => 'KN202500StockItemSummary',
                                    'KN202500StockItemSummaryServiceCommands' => 'KN202500StockItemSummaryServiceCommands',
                                    'KN202500GeneralSettingsItemDefaults' => 'KN202500GeneralSettingsItemDefaults',
                                    'KN202500GeneralSettingsItemDefaultsServiceCommands' => 'KN202500GeneralSettingsItemDefaultsServiceCommands',
                                    'KN202500GeneralSettingsWarehouseDefaults' => 'KN202500GeneralSettingsWarehouseDefaults',
                                    'KN202500GeneralSettingsWarehouseDefaultsServiceCommands' => 'KN202500GeneralSettingsWarehouseDefaultsServiceCommands',
                                    'KN202500GeneralSettingsSubItemsInformation' => 'KN202500GeneralSettingsSubItemsInformation',
                                    'KN202500GeneralSettingsSubItemsInformationServiceCommands' => 'KN202500GeneralSettingsSubItemsInformationServiceCommands',
                                    'KN202500GeneralSettingsUnitOfMeasure' => 'KN202500GeneralSettingsUnitOfMeasure',
                                    'KN202500GeneralSettingsUnitOfMeasureServiceCommands' => 'KN202500GeneralSettingsUnitOfMeasureServiceCommands',
                                    'KN202500PriceCostInfoPriceManagement' => 'KN202500PriceCostInfoPriceManagement',
                                    'KN202500PriceCostInfoPriceManagementServiceCommands' => 'KN202500PriceCostInfoPriceManagementServiceCommands',
                                    'KN202500PriceCostInfoStandardCost' => 'KN202500PriceCostInfoStandardCost',
                                    'KN202500PriceCostInfoStandardCostServiceCommands' => 'KN202500PriceCostInfoStandardCostServiceCommands',
                                    'KN202500PackagingDimensions' => 'KN202500PackagingDimensions',
                                    'KN202500PackagingDimensionsServiceCommands' => 'KN202500PackagingDimensionsServiceCommands',
                                    'KN202500PackagingAutomaticPackaging' => 'KN202500PackagingAutomaticPackaging',
                                    'KN202500PackagingAutomaticPackagingServiceCommands' => 'KN202500PackagingAutomaticPackagingServiceCommands',
                                    'KN202500Attributes' => 'KN202500Attributes',
                                    'KN202500AttributesServiceCommands' => 'KN202500AttributesServiceCommands',
                                    'KN202500ConfigurableProduct' => 'KN202500ConfigurableProduct',
                                    'KN202500ConfigurableProductServiceCommands' => 'KN202500ConfigurableProductServiceCommands',
                                    'KN202500Ecommerce' => 'KN202500Ecommerce',
                                    'KN202500EcommerceServiceCommands' => 'KN202500EcommerceServiceCommands',
                                    'KN202500PackagingAutomaticPackagingBoxes' => 'KN202500PackagingAutomaticPackagingBoxes',
                                    'KN202500PackagingAutomaticPackagingBoxesServiceCommands' => 'KN202500PackagingAutomaticPackagingBoxesServiceCommands',
                                    'KN202500AttributesAttributes' => 'KN202500AttributesAttributes',
                                    'KN202500AttributesAttributesServiceCommands' => 'KN202500AttributesAttributesServiceCommands',
                                    'KN202500AttributesSalesCategories' => 'KN202500AttributesSalesCategories',
                                    'KN202500AttributesSalesCategoriesServiceCommands' => 'KN202500AttributesSalesCategoriesServiceCommands',
                                    'KN202500SalesCategorySalesCategories' => 'KN202500SalesCategorySalesCategories',
                                    'KN202500SalesCategorySalesCategoriesServiceCommands' => 'KN202500SalesCategorySalesCategoriesServiceCommands',
                                    'KN202500BundleProductMappedItemImage' => 'KN202500BundleProductMappedItemImage',
                                    'KN202500BundleProductMappedItemImageServiceCommands' => 'KN202500BundleProductMappedItemImageServiceCommands',
                                    'KN202500GroupedProductMappedItemImage' => 'KN202500GroupedProductMappedItemImage',
                                    'KN202500GroupedProductMappedItemImageServiceCommands' => 'KN202500GroupedProductMappedItemImageServiceCommands',
                                    'KN202500Subitems' => 'KN202500Subitems',
                                    'KN202500SubitemsServiceCommands' => 'KN202500SubitemsServiceCommands',
                                    'KN202500GroupedProduct' => 'KN202500GroupedProduct',
                                    'KN202500GroupedProductServiceCommands' => 'KN202500GroupedProductServiceCommands',
                                    'KN202500DownloadableProductMappedInventory' => 'KN202500DownloadableProductMappedInventory',
                                    'KN202500DownloadableProductMappedInventoryServiceCommands' => 'KN202500DownloadableProductMappedInventoryServiceCommands',
                                    'KN202500ConfigurableProductMappedSimpleInventory' => 'KN202500ConfigurableProductMappedSimpleInventory',
                                    'KN202500ConfigurableProductMappedSimpleInventoryServiceCommands' => 'KN202500ConfigurableProductMappedSimpleInventoryServiceCommands',
                                    'KN202500ConfigurableProductAttributeList' => 'KN202500ConfigurableProductAttributeList',
                                    'KN202500ConfigurableProductAttributeListServiceCommands' => 'KN202500ConfigurableProductAttributeListServiceCommands',
                                    'KN202500ConfigurableProductNewItems' => 'KN202500ConfigurableProductNewItems',
                                    'KN202500ConfigurableProductNewItemsServiceCommands' => 'KN202500ConfigurableProductNewItemsServiceCommands',
                                    'KN202500CreateProductLookup' => 'KN202500CreateProductLookup',
                                    'KN202500CreateProductLookupServiceCommands' => 'KN202500CreateProductLookupServiceCommands',
                                    'KN202500BundleProductDefinedOptions' => 'KN202500BundleProductDefinedOptions',
                                    'KN202500BundleProductDefinedOptionsServiceCommands' => 'KN202500BundleProductDefinedOptionsServiceCommands',
                                    'KN202500BundleProductMappedInventoriesForSelectedOption' => 'KN202500BundleProductMappedInventoriesForSelectedOption',
                                    'KN202500BundleProductMappedInventoriesForSelectedOptionServiceCommands' => 'KN202500BundleProductMappedInventoriesForSelectedOptionServiceCommands',
                                    'KN202500BundleProductRulesCreationLookupConditions' => 'KN202500BundleProductRulesCreationLookupConditions',
                                    'KN202500BundleProductRulesCreationLookupConditionsServiceCommands' => 'KN202500BundleProductRulesCreationLookupConditionsServiceCommands',
                                    'KN202500EcommerceCrossSells' => 'KN202500EcommerceCrossSells',
                                    'KN202500EcommerceCrossSellsServiceCommands' => 'KN202500EcommerceCrossSellsServiceCommands',
                                    'KN202500EcommerceUpSells' => 'KN202500EcommerceUpSells',
                                    'KN202500EcommerceUpSellsServiceCommands' => 'KN202500EcommerceUpSellsServiceCommands',
                                    'KN202500GeneralSettingsUnitOfMeasureAdditionalCharges' => 'KN202500GeneralSettingsUnitOfMeasureAdditionalCharges',
                                    'KN202500GeneralSettingsUnitOfMeasureAdditionalChargesServiceCommands' => 'KN202500GeneralSettingsUnitOfMeasureAdditionalChargesServiceCommands',
                                    'KN202500PriceCostInfoCostStatisticsItemCostStatistics' => 'KN202500PriceCostInfoCostStatisticsItemCostStatistics',
                                    'KN202500PriceCostInfoCostStatisticsItemCostStatisticsServiceCommands' => 'KN202500PriceCostInfoCostStatisticsItemCostStatisticsServiceCommands',
                                    'KN202500PrimaryKey' => 'KN202500PrimaryKey',
                                    'KN202500Clear' => 'KN202500Clear',
                                    'KN202500ClearResponse' => 'KN202500ClearResponse',
                                    'KN202500GetProcessStatus' => 'KN202500GetProcessStatus',
                                    'KN202500GetProcessStatusResponse' => 'KN202500GetProcessStatusResponse',
                                    'KN202500GetSchema' => 'KN202500GetSchema',
                                    'KN202500GetSchemaResponse' => 'KN202500GetSchemaResponse',
                                    'KN202500SetSchema' => 'KN202500SetSchema',
                                    'KN202500SetSchemaResponse' => 'KN202500SetSchemaResponse',
                                    'KN202500Export' => 'KN202500Export',
                                    'KN202500ExportResponse' => 'KN202500ExportResponse',
                                    'KN202500Import' => 'KN202500Import',
                                    'KN202500ImportResponse' => 'KN202500ImportResponse',
                                    'KN202500ImportResult' => 'KN202500ImportResult',
                                    'KN202500ArrayOfImportResult' => 'KN202500ArrayOfImportResult',
                                    'KN202500Submit' => 'KN202500Submit',
                                    'KN202500ArrayOfContent' => 'KN202500ArrayOfContent',
                                    'KN202500SubmitResponse' => 'KN202500SubmitResponse',
                                    'KN505888Content' => 'KN505888Content',
                                    'KN505888Actions' => 'KN505888Actions',
                                    'KN505888CategoryInfo' => 'KN505888CategoryInfo',
                                    'KN505888CategoryInfoServiceCommands' => 'KN505888CategoryInfoServiceCommands',
                                    'KN505888CategoryDetails' => 'KN505888CategoryDetails',
                                    'KN505888CategoryDetailsServiceCommands' => 'KN505888CategoryDetailsServiceCommands',
                                    'KN505888CategoryMembers' => 'KN505888CategoryMembers',
                                    'KN505888CategoryMembersServiceCommands' => 'KN505888CategoryMembersServiceCommands',
                                    'KN505888Clear' => 'KN505888Clear',
                                    'KN505888ClearResponse' => 'KN505888ClearResponse',
                                    'KN505888GetProcessStatus' => 'KN505888GetProcessStatus',
                                    'KN505888GetProcessStatusResponse' => 'KN505888GetProcessStatusResponse',
                                    'KN505888GetSchema' => 'KN505888GetSchema',
                                    'KN505888GetSchemaResponse' => 'KN505888GetSchemaResponse',
                                    'KN505888SetSchema' => 'KN505888SetSchema',
                                    'KN505888SetSchemaResponse' => 'KN505888SetSchemaResponse',
                                    'KN505888Export' => 'KN505888Export',
                                    'KN505888ExportResponse' => 'KN505888ExportResponse',
                                    'KN505888Import' => 'KN505888Import',
                                    'KN505888ImportResponse' => 'KN505888ImportResponse',
                                    'KN505888ImportResult' => 'KN505888ImportResult',
                                    'KN505888ArrayOfImportResult' => 'KN505888ArrayOfImportResult',
                                    'KN505888Submit' => 'KN505888Submit',
                                    'KN505888ArrayOfContent' => 'KN505888ArrayOfContent',
                                    'KN505888SubmitResponse' => 'KN505888SubmitResponse',
                                    'SO301000Content' => 'SO301000Content',
                                    'SO301000Actions' => 'SO301000Actions',
                                    'SO301000OrderSummary' => 'SO301000OrderSummary',
                                    'SO301000OrderSummaryServiceCommands' => 'SO301000OrderSummaryServiceCommands',
                                    'SO301000CommissionsDefaultSalesperson' => 'SO301000CommissionsDefaultSalesperson',
                                    'SO301000CommissionsDefaultSalespersonServiceCommands' => 'SO301000CommissionsDefaultSalespersonServiceCommands',
                                    'SO301000FinancialSettingsFinancialInformation' => 'SO301000FinancialSettingsFinancialInformation',
                                    'SO301000FinancialSettingsFinancialInformationServiceCommands' => 'SO301000FinancialSettingsFinancialInformationServiceCommands',
                                    'SO301000PaymentSettings' => 'SO301000PaymentSettings',
                                    'SO301000PaymentSettingsServiceCommands' => 'SO301000PaymentSettingsServiceCommands',
                                    'SO301000ShippingSettingsShippingInformation' => 'SO301000ShippingSettingsShippingInformation',
                                    'SO301000ShippingSettingsShippingInformationServiceCommands' => 'SO301000ShippingSettingsShippingInformationServiceCommands',
                                    'SO301000Totals' => 'SO301000Totals',
                                    'SO301000TotalsServiceCommands' => 'SO301000TotalsServiceCommands',
                                    'SO301000ShopForRatesServicesSettings' => 'SO301000ShopForRatesServicesSettings',
                                    'SO301000ShopForRatesServicesSettingsServiceCommands' => 'SO301000ShopForRatesServicesSettingsServiceCommands',
                                    'SO301000ShopForRatesServicesSettingsIsManualPackage' => 'SO301000ShopForRatesServicesSettingsIsManualPackage',
                                    'SO301000ShopForRatesServicesSettingsIsManualPackageServiceCommands' => 'SO301000ShopForRatesServicesSettingsIsManualPackageServiceCommands',
                                    'SO301000DocumentDetails' => 'SO301000DocumentDetails',
                                    'SO301000DocumentDetailsServiceCommands' => 'SO301000DocumentDetailsServiceCommands',
                                    'SO301000PurchasingDetailsPurchasingSettings' => 'SO301000PurchasingDetailsPurchasingSettings',
                                    'SO301000PurchasingDetailsPurchasingSettingsServiceCommands' => 'SO301000PurchasingDetailsPurchasingSettingsServiceCommands',
                                    'SO301000TaxDetails' => 'SO301000TaxDetails',
                                    'SO301000TaxDetailsServiceCommands' => 'SO301000TaxDetailsServiceCommands',
                                    'SO301000Shipments' => 'SO301000Shipments',
                                    'SO301000ShipmentsServiceCommands' => 'SO301000ShipmentsServiceCommands',
                                    'SO301000FinancialSettingsBillToInfo' => 'SO301000FinancialSettingsBillToInfo',
                                    'SO301000FinancialSettingsBillToInfoServiceCommands' => 'SO301000FinancialSettingsBillToInfoServiceCommands',
                                    'SO301000ShippingSettingsShipToInfo' => 'SO301000ShippingSettingsShipToInfo',
                                    'SO301000ShippingSettingsShipToInfoServiceCommands' => 'SO301000ShippingSettingsShipToInfoServiceCommands',
                                    'SO301000FinancialSettingsBillToInfoOverrideContact' => 'SO301000FinancialSettingsBillToInfoOverrideContact',
                                    'SO301000FinancialSettingsBillToInfoOverrideContactServiceCommands' => 'SO301000FinancialSettingsBillToInfoOverrideContactServiceCommands',
                                    'SO301000ShippingSettingsShipToInfoOverrideContact' => 'SO301000ShippingSettingsShipToInfoOverrideContact',
                                    'SO301000ShippingSettingsShipToInfoOverrideContactServiceCommands' => 'SO301000ShippingSettingsShipToInfoOverrideContactServiceCommands',
                                    'SO301000ApprovalDetails' => 'SO301000ApprovalDetails',
                                    'SO301000ApprovalDetailsServiceCommands' => 'SO301000ApprovalDetailsServiceCommands',
                                    'SO301000OrderSummaryRateSelection' => 'SO301000OrderSummaryRateSelection',
                                    'SO301000OrderSummaryRateSelectionServiceCommands' => 'SO301000OrderSummaryRateSelectionServiceCommands',
                                    'SO301000OrderSummaryRateSelectionCurrencyUnitEquivalents' => 'SO301000OrderSummaryRateSelectionCurrencyUnitEquivalents',
                                    'SO301000OrderSummaryRateSelectionCurrencyUnitEquivalentsServiceCommands' => 'SO301000OrderSummaryRateSelectionCurrencyUnitEquivalentsServiceCommands',
                                    'SO301000Allocations' => 'SO301000Allocations',
                                    'SO301000AllocationsServiceCommands' => 'SO301000AllocationsServiceCommands',
                                    'SO301000DiscountDetails' => 'SO301000DiscountDetails',
                                    'SO301000DiscountDetailsServiceCommands' => 'SO301000DiscountDetailsServiceCommands',
                                    'SO301000CompositeItemsConfigurationLookup' => 'SO301000CompositeItemsConfigurationLookup',
                                    'SO301000CompositeItemsConfigurationLookupServiceCommands' => 'SO301000CompositeItemsConfigurationLookupServiceCommands',
                                    'SO301000CompositeItemsConfigurationLookupSelected' => 'SO301000CompositeItemsConfigurationLookupSelected',
                                    'SO301000CompositeItemsConfigurationLookupSelectedServiceCommands' => 'SO301000CompositeItemsConfigurationLookupSelectedServiceCommands',
                                    'SO301000SpecifyShipmentParameters' => 'SO301000SpecifyShipmentParameters',
                                    'SO301000SpecifyShipmentParametersServiceCommands' => 'SO301000SpecifyShipmentParametersServiceCommands',
                                    'SO301000AddInvoiceDetailsDocType' => 'SO301000AddInvoiceDetailsDocType',
                                    'SO301000AddInvoiceDetailsDocTypeServiceCommands' => 'SO301000AddInvoiceDetailsDocTypeServiceCommands',
                                    'SO301000CopyTo' => 'SO301000CopyTo',
                                    'SO301000CopyToServiceCommands' => 'SO301000CopyToServiceCommands',
                                    'SO301000RecalculatePrices' => 'SO301000RecalculatePrices',
                                    'SO301000RecalculatePricesServiceCommands' => 'SO301000RecalculatePricesServiceCommands',
                                    'SO301000AddInvoiceDetails' => 'SO301000AddInvoiceDetails',
                                    'SO301000AddInvoiceDetailsServiceCommands' => 'SO301000AddInvoiceDetailsServiceCommands',
                                    'SO301000PurchasingDetails' => 'SO301000PurchasingDetails',
                                    'SO301000PurchasingDetailsServiceCommands' => 'SO301000PurchasingDetailsServiceCommands',
                                    'SO301000Commissions' => 'SO301000Commissions',
                                    'SO301000CommissionsServiceCommands' => 'SO301000CommissionsServiceCommands',
                                    'SO301000ShopForRatesPackages' => 'SO301000ShopForRatesPackages',
                                    'SO301000ShopForRatesPackagesServiceCommands' => 'SO301000ShopForRatesPackagesServiceCommands',
                                    'SO301000ShopForRatesCarrierRates' => 'SO301000ShopForRatesCarrierRates',
                                    'SO301000ShopForRatesCarrierRatesServiceCommands' => 'SO301000ShopForRatesCarrierRatesServiceCommands',
                                    'SO301000Payments' => 'SO301000Payments',
                                    'SO301000PaymentsServiceCommands' => 'SO301000PaymentsServiceCommands',
                                    'SO301000PaymentSettingsInputMode' => 'SO301000PaymentSettingsInputMode',
                                    'SO301000PaymentSettingsInputModeServiceCommands' => 'SO301000PaymentSettingsInputModeServiceCommands',
                                    'SO301000PaymentSettingsCardInfo' => 'SO301000PaymentSettingsCardInfo',
                                    'SO301000PaymentSettingsCardInfoServiceCommands' => 'SO301000PaymentSettingsCardInfoServiceCommands',
                                    'SO301000PaymentSettingsDescription' => 'SO301000PaymentSettingsDescription',
                                    'SO301000PaymentSettingsDescriptionServiceCommands' => 'SO301000PaymentSettingsDescriptionServiceCommands',
                                    'SO301000InventoryLookupInventory' => 'SO301000InventoryLookupInventory',
                                    'SO301000InventoryLookupInventoryServiceCommands' => 'SO301000InventoryLookupInventoryServiceCommands',
                                    'SO301000InventoryLookup' => 'SO301000InventoryLookup',
                                    'SO301000InventoryLookupServiceCommands' => 'SO301000InventoryLookupServiceCommands',
                                    'SO301000CompositeItemsConfigurationLookupType' => 'SO301000CompositeItemsConfigurationLookupType',
                                    'SO301000CompositeItemsConfigurationLookupTypeServiceCommands' => 'SO301000CompositeItemsConfigurationLookupTypeServiceCommands',
                                    'SO301000CompositeItemsConfigurationLookupType_' => 'SO301000CompositeItemsConfigurationLookupType_',
                                    'SO301000CompositeItemsConfigurationLookupType_ServiceCommands' => 'SO301000CompositeItemsConfigurationLookupType_ServiceCommands',
                                    'SO301000CompositeItemsConfigurationLookupCompositeNotes' => 'SO301000CompositeItemsConfigurationLookupCompositeNotes',
                                    'SO301000CompositeItemsConfigurationLookupCompositeNotesServiceCommands' => 'SO301000CompositeItemsConfigurationLookupCompositeNotesServiceCommands',
                                    'SO301000CompositeItemsConfigurationLookup_' => 'SO301000CompositeItemsConfigurationLookup_',
                                    'SO301000CompositeItemsConfigurationLookup_ServiceCommands' => 'SO301000CompositeItemsConfigurationLookup_ServiceCommands',
                                    'SO301000CompositeItemsConfigurationLookupAvailableOptions' => 'SO301000CompositeItemsConfigurationLookupAvailableOptions',
                                    'SO301000CompositeItemsConfigurationLookupAvailableOptionsServiceCommands' => 'SO301000CompositeItemsConfigurationLookupAvailableOptionsServiceCommands',
                                    'SO301000CompositeItemsConfigurationLookupConfiguredRules' => 'SO301000CompositeItemsConfigurationLookupConfiguredRules',
                                    'SO301000CompositeItemsConfigurationLookupConfiguredRulesServiceCommands' => 'SO301000CompositeItemsConfigurationLookupConfiguredRulesServiceCommands',
                                    'SO301000CompositeItemsConfigurationLookupSelectedItemsThisIncludesMustItems' => 'SO301000CompositeItemsConfigurationLookupSelectedItemsThisIncludesMustItems',
                                    'SO301000CompositeItemsConfigurationLookupSelectedItemsThisIncludesMustItemsServiceCommands' => 'SO301000CompositeItemsConfigurationLookupSelectedItemsThisIncludesMustItemsServiceCommands',
                                    'SO301000AllocationsUnassignedQty' => 'SO301000AllocationsUnassignedQty',
                                    'SO301000AllocationsUnassignedQtyServiceCommands' => 'SO301000AllocationsUnassignedQtyServiceCommands',
                                    'SO301000PrimaryKey' => 'SO301000PrimaryKey',
                                    'SO301000Clear' => 'SO301000Clear',
                                    'SO301000ClearResponse' => 'SO301000ClearResponse',
                                    'SO301000GetProcessStatus' => 'SO301000GetProcessStatus',
                                    'SO301000GetProcessStatusResponse' => 'SO301000GetProcessStatusResponse',
                                    'SO301000GetSchema' => 'SO301000GetSchema',
                                    'SO301000GetSchemaResponse' => 'SO301000GetSchemaResponse',
                                    'SO301000SetSchema' => 'SO301000SetSchema',
                                    'SO301000SetSchemaResponse' => 'SO301000SetSchemaResponse',
                                    'SO301000Export' => 'SO301000Export',
                                    'SO301000ExportResponse' => 'SO301000ExportResponse',
                                    'SO301000Import' => 'SO301000Import',
                                    'SO301000ImportResponse' => 'SO301000ImportResponse',
                                    'SO301000ImportResult' => 'SO301000ImportResult',
                                    'SO301000ArrayOfImportResult' => 'SO301000ArrayOfImportResult',
                                    'SO301000Submit' => 'SO301000Submit',
                                    'SO301000ArrayOfContent' => 'SO301000ArrayOfContent',
                                    'SO301000SubmitResponse' => 'SO301000SubmitResponse',
                                   );

  public function Screen($wsdl = "http://192.168.10.18:146/ACM5301583/Soap/CONNECTOR2.asmx?WSDL", $options = array()) {
    foreach(self::$classmap as $key => $value) {
      if(!isset($options['classmap'][$key])) {
        $options['classmap'][$key] = $value;
      }
    }
    parent::__construct($wsdl, $options);
  }

  /**
   *  
   *
   * @param GetScenario $parameters
   * @return GetScenarioResponse
   */
  public function GetScenario(GetScenario $parameters) {
    return $this->__soapCall('GetScenario', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param Login $parameters
   * @return LoginResponse
   */
  public function Login(Login $parameters) {
    return $this->__soapCall('Login', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param Logout $parameters
   * @return LogoutResponse
   */
  public function Logout(Logout $parameters) {
    return $this->__soapCall('Logout', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SetBusinessDate $parameters
   * @return SetBusinessDateResponse
   */
  public function SetBusinessDate(SetBusinessDate $parameters) {
    return $this->__soapCall('SetBusinessDate', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SetLocaleName $parameters
   * @return SetLocaleNameResponse
   */
  public function SetLocaleName(SetLocaleName $parameters) {
    return $this->__soapCall('SetLocaleName', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SetSchemaMode $parameters
   * @return SetSchemaModeResponse
   */
  public function SetSchemaMode(SetSchemaMode $parameters) {
    return $this->__soapCall('SetSchemaMode', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GI000020Clear $parameters
   * @return GI000020ClearResponse
   */
  public function GI000020Clear(GI000020Clear $parameters) {
    return $this->__soapCall('GI000020Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GI000020GetProcessStatus $parameters
   * @return GI000020GetProcessStatusResponse
   */
  public function GI000020GetProcessStatus(GI000020GetProcessStatus $parameters) {
    return $this->__soapCall('GI000020GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GI000020GetSchema $parameters
   * @return GI000020GetSchemaResponse
   */
  public function GI000020GetSchema(GI000020GetSchema $parameters) {
    return $this->__soapCall('GI000020GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GI000020SetSchema $parameters
   * @return GI000020SetSchemaResponse
   */
  public function GI000020SetSchema(GI000020SetSchema $parameters) {
    return $this->__soapCall('GI000020SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GI000020Export $parameters
   * @return GI000020ExportResponse
   */
  public function GI000020Export(GI000020Export $parameters) {
    return $this->__soapCall('GI000020Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GI000020Import $parameters
   * @return GI000020ImportResponse
   */
  public function GI000020Import(GI000020Import $parameters) {
    return $this->__soapCall('GI000020Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GI000020Submit $parameters
   * @return GI000020SubmitResponse
   */
  public function GI000020Submit(GI000020Submit $parameters) {
    return $this->__soapCall('GI000020Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS05Clear $parameters
   * @return GIKEMS05ClearResponse
   */
  public function GIKEMS05Clear(GIKEMS05Clear $parameters) {
    return $this->__soapCall('GIKEMS05Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS05GetProcessStatus $parameters
   * @return GIKEMS05GetProcessStatusResponse
   */
  public function GIKEMS05GetProcessStatus(GIKEMS05GetProcessStatus $parameters) {
    return $this->__soapCall('GIKEMS05GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS05GetSchema $parameters
   * @return GIKEMS05GetSchemaResponse
   */
  public function GIKEMS05GetSchema(GIKEMS05GetSchema $parameters) {
    return $this->__soapCall('GIKEMS05GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS05SetSchema $parameters
   * @return GIKEMS05SetSchemaResponse
   */
  public function GIKEMS05SetSchema(GIKEMS05SetSchema $parameters) {
    return $this->__soapCall('GIKEMS05SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS05Export $parameters
   * @return GIKEMS05ExportResponse
   */
  public function GIKEMS05Export(GIKEMS05Export $parameters) {
    return $this->__soapCall('GIKEMS05Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS05Import $parameters
   * @return GIKEMS05ImportResponse
   */
  public function GIKEMS05Import(GIKEMS05Import $parameters) {
    return $this->__soapCall('GIKEMS05Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS05Submit $parameters
   * @return GIKEMS05SubmitResponse
   */
  public function GIKEMS05Submit(GIKEMS05Submit $parameters) {
    return $this->__soapCall('GIKEMS05Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS06Clear $parameters
   * @return GIKEMS06ClearResponse
   */
  public function GIKEMS06Clear(GIKEMS06Clear $parameters) {
    return $this->__soapCall('GIKEMS06Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS06GetProcessStatus $parameters
   * @return GIKEMS06GetProcessStatusResponse
   */
  public function GIKEMS06GetProcessStatus(GIKEMS06GetProcessStatus $parameters) {
    return $this->__soapCall('GIKEMS06GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS06GetSchema $parameters
   * @return GIKEMS06GetSchemaResponse
   */
  public function GIKEMS06GetSchema(GIKEMS06GetSchema $parameters) {
    return $this->__soapCall('GIKEMS06GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS06SetSchema $parameters
   * @return GIKEMS06SetSchemaResponse
   */
  public function GIKEMS06SetSchema(GIKEMS06SetSchema $parameters) {
    return $this->__soapCall('GIKEMS06SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS06Export $parameters
   * @return GIKEMS06ExportResponse
   */
  public function GIKEMS06Export(GIKEMS06Export $parameters) {
    return $this->__soapCall('GIKEMS06Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS06Import $parameters
   * @return GIKEMS06ImportResponse
   */
  public function GIKEMS06Import(GIKEMS06Import $parameters) {
    return $this->__soapCall('GIKEMS06Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS06Submit $parameters
   * @return GIKEMS06SubmitResponse
   */
  public function GIKEMS06Submit(GIKEMS06Submit $parameters) {
    return $this->__soapCall('GIKEMS06Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS07Clear $parameters
   * @return GIKEMS07ClearResponse
   */
  public function GIKEMS07Clear(GIKEMS07Clear $parameters) {
    return $this->__soapCall('GIKEMS07Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS07GetProcessStatus $parameters
   * @return GIKEMS07GetProcessStatusResponse
   */
  public function GIKEMS07GetProcessStatus(GIKEMS07GetProcessStatus $parameters) {
    return $this->__soapCall('GIKEMS07GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS07GetSchema $parameters
   * @return GIKEMS07GetSchemaResponse
   */
  public function GIKEMS07GetSchema(GIKEMS07GetSchema $parameters) {
    return $this->__soapCall('GIKEMS07GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS07SetSchema $parameters
   * @return GIKEMS07SetSchemaResponse
   */
  public function GIKEMS07SetSchema(GIKEMS07SetSchema $parameters) {
    return $this->__soapCall('GIKEMS07SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS07Export $parameters
   * @return GIKEMS07ExportResponse
   */
  public function GIKEMS07Export(GIKEMS07Export $parameters) {
    return $this->__soapCall('GIKEMS07Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS07Import $parameters
   * @return GIKEMS07ImportResponse
   */
  public function GIKEMS07Import(GIKEMS07Import $parameters) {
    return $this->__soapCall('GIKEMS07Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS07Submit $parameters
   * @return GIKEMS07SubmitResponse
   */
  public function GIKEMS07Submit(GIKEMS07Submit $parameters) {
    return $this->__soapCall('GIKEMS07Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS08Clear $parameters
   * @return GIKEMS08ClearResponse
   */
  public function GIKEMS08Clear(GIKEMS08Clear $parameters) {
    return $this->__soapCall('GIKEMS08Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS08GetProcessStatus $parameters
   * @return GIKEMS08GetProcessStatusResponse
   */
  public function GIKEMS08GetProcessStatus(GIKEMS08GetProcessStatus $parameters) {
    return $this->__soapCall('GIKEMS08GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS08GetSchema $parameters
   * @return GIKEMS08GetSchemaResponse
   */
  public function GIKEMS08GetSchema(GIKEMS08GetSchema $parameters) {
    return $this->__soapCall('GIKEMS08GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS08SetSchema $parameters
   * @return GIKEMS08SetSchemaResponse
   */
  public function GIKEMS08SetSchema(GIKEMS08SetSchema $parameters) {
    return $this->__soapCall('GIKEMS08SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS08Export $parameters
   * @return GIKEMS08ExportResponse
   */
  public function GIKEMS08Export(GIKEMS08Export $parameters) {
    return $this->__soapCall('GIKEMS08Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS08Import $parameters
   * @return GIKEMS08ImportResponse
   */
  public function GIKEMS08Import(GIKEMS08Import $parameters) {
    return $this->__soapCall('GIKEMS08Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS08Submit $parameters
   * @return GIKEMS08SubmitResponse
   */
  public function GIKEMS08Submit(GIKEMS08Submit $parameters) {
    return $this->__soapCall('GIKEMS08Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS10Clear $parameters
   * @return GIKEMS10ClearResponse
   */
  public function GIKEMS10Clear(GIKEMS10Clear $parameters) {
    return $this->__soapCall('GIKEMS10Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS10GetProcessStatus $parameters
   * @return GIKEMS10GetProcessStatusResponse
   */
  public function GIKEMS10GetProcessStatus(GIKEMS10GetProcessStatus $parameters) {
    return $this->__soapCall('GIKEMS10GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS10GetSchema $parameters
   * @return GIKEMS10GetSchemaResponse
   */
  public function GIKEMS10GetSchema(GIKEMS10GetSchema $parameters) {
    return $this->__soapCall('GIKEMS10GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS10SetSchema $parameters
   * @return GIKEMS10SetSchemaResponse
   */
  public function GIKEMS10SetSchema(GIKEMS10SetSchema $parameters) {
    return $this->__soapCall('GIKEMS10SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS10Export $parameters
   * @return GIKEMS10ExportResponse
   */
  public function GIKEMS10Export(GIKEMS10Export $parameters) {
    return $this->__soapCall('GIKEMS10Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS10Import $parameters
   * @return GIKEMS10ImportResponse
   */
  public function GIKEMS10Import(GIKEMS10Import $parameters) {
    return $this->__soapCall('GIKEMS10Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS10Submit $parameters
   * @return GIKEMS10SubmitResponse
   */
  public function GIKEMS10Submit(GIKEMS10Submit $parameters) {
    return $this->__soapCall('GIKEMS10Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS12Clear $parameters
   * @return GIKEMS12ClearResponse
   */
  public function GIKEMS12Clear(GIKEMS12Clear $parameters) {
    return $this->__soapCall('GIKEMS12Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS12GetProcessStatus $parameters
   * @return GIKEMS12GetProcessStatusResponse
   */
  public function GIKEMS12GetProcessStatus(GIKEMS12GetProcessStatus $parameters) {
    return $this->__soapCall('GIKEMS12GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS12GetSchema $parameters
   * @return GIKEMS12GetSchemaResponse
   */
  public function GIKEMS12GetSchema(GIKEMS12GetSchema $parameters) {
    return $this->__soapCall('GIKEMS12GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS12SetSchema $parameters
   * @return GIKEMS12SetSchemaResponse
   */
  public function GIKEMS12SetSchema(GIKEMS12SetSchema $parameters) {
    return $this->__soapCall('GIKEMS12SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS12Export $parameters
   * @return GIKEMS12ExportResponse
   */
  public function GIKEMS12Export(GIKEMS12Export $parameters) {
    return $this->__soapCall('GIKEMS12Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS12Import $parameters
   * @return GIKEMS12ImportResponse
   */
  public function GIKEMS12Import(GIKEMS12Import $parameters) {
    return $this->__soapCall('GIKEMS12Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS12Submit $parameters
   * @return GIKEMS12SubmitResponse
   */
  public function GIKEMS12Submit(GIKEMS12Submit $parameters) {
    return $this->__soapCall('GIKEMS12Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS13Clear $parameters
   * @return GIKEMS13ClearResponse
   */
  public function GIKEMS13Clear(GIKEMS13Clear $parameters) {
    return $this->__soapCall('GIKEMS13Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS13GetProcessStatus $parameters
   * @return GIKEMS13GetProcessStatusResponse
   */
  public function GIKEMS13GetProcessStatus(GIKEMS13GetProcessStatus $parameters) {
    return $this->__soapCall('GIKEMS13GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS13GetSchema $parameters
   * @return GIKEMS13GetSchemaResponse
   */
  public function GIKEMS13GetSchema(GIKEMS13GetSchema $parameters) {
    return $this->__soapCall('GIKEMS13GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS13SetSchema $parameters
   * @return GIKEMS13SetSchemaResponse
   */
  public function GIKEMS13SetSchema(GIKEMS13SetSchema $parameters) {
    return $this->__soapCall('GIKEMS13SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS13Export $parameters
   * @return GIKEMS13ExportResponse
   */
  public function GIKEMS13Export(GIKEMS13Export $parameters) {
    return $this->__soapCall('GIKEMS13Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS13Import $parameters
   * @return GIKEMS13ImportResponse
   */
  public function GIKEMS13Import(GIKEMS13Import $parameters) {
    return $this->__soapCall('GIKEMS13Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS13Submit $parameters
   * @return GIKEMS13SubmitResponse
   */
  public function GIKEMS13Submit(GIKEMS13Submit $parameters) {
    return $this->__soapCall('GIKEMS13Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS16Clear $parameters
   * @return GIKEMS16ClearResponse
   */
  public function GIKEMS16Clear(GIKEMS16Clear $parameters) {
    return $this->__soapCall('GIKEMS16Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS16GetProcessStatus $parameters
   * @return GIKEMS16GetProcessStatusResponse
   */
  public function GIKEMS16GetProcessStatus(GIKEMS16GetProcessStatus $parameters) {
    return $this->__soapCall('GIKEMS16GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS16GetSchema $parameters
   * @return GIKEMS16GetSchemaResponse
   */
  public function GIKEMS16GetSchema(GIKEMS16GetSchema $parameters) {
    return $this->__soapCall('GIKEMS16GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS16SetSchema $parameters
   * @return GIKEMS16SetSchemaResponse
   */
  public function GIKEMS16SetSchema(GIKEMS16SetSchema $parameters) {
    return $this->__soapCall('GIKEMS16SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS16Export $parameters
   * @return GIKEMS16ExportResponse
   */
  public function GIKEMS16Export(GIKEMS16Export $parameters) {
    return $this->__soapCall('GIKEMS16Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS16Import $parameters
   * @return GIKEMS16ImportResponse
   */
  public function GIKEMS16Import(GIKEMS16Import $parameters) {
    return $this->__soapCall('GIKEMS16Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS16Submit $parameters
   * @return GIKEMS16SubmitResponse
   */
  public function GIKEMS16Submit(GIKEMS16Submit $parameters) {
    return $this->__soapCall('GIKEMS16Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS18Clear $parameters
   * @return GIKEMS18ClearResponse
   */
  public function GIKEMS18Clear(GIKEMS18Clear $parameters) {
    return $this->__soapCall('GIKEMS18Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS18GetProcessStatus $parameters
   * @return GIKEMS18GetProcessStatusResponse
   */
  public function GIKEMS18GetProcessStatus(GIKEMS18GetProcessStatus $parameters) {
    return $this->__soapCall('GIKEMS18GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS18GetSchema $parameters
   * @return GIKEMS18GetSchemaResponse
   */
  public function GIKEMS18GetSchema(GIKEMS18GetSchema $parameters) {
    return $this->__soapCall('GIKEMS18GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS18SetSchema $parameters
   * @return GIKEMS18SetSchemaResponse
   */
  public function GIKEMS18SetSchema(GIKEMS18SetSchema $parameters) {
    return $this->__soapCall('GIKEMS18SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS18Export $parameters
   * @return GIKEMS18ExportResponse
   */
  public function GIKEMS18Export(GIKEMS18Export $parameters) {
    return $this->__soapCall('GIKEMS18Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS18Import $parameters
   * @return GIKEMS18ImportResponse
   */
  public function GIKEMS18Import(GIKEMS18Import $parameters) {
    return $this->__soapCall('GIKEMS18Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS18Submit $parameters
   * @return GIKEMS18SubmitResponse
   */
  public function GIKEMS18Submit(GIKEMS18Submit $parameters) {
    return $this->__soapCall('GIKEMS18Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS19Clear $parameters
   * @return GIKEMS19ClearResponse
   */
  public function GIKEMS19Clear(GIKEMS19Clear $parameters) {
    return $this->__soapCall('GIKEMS19Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS19GetProcessStatus $parameters
   * @return GIKEMS19GetProcessStatusResponse
   */
  public function GIKEMS19GetProcessStatus(GIKEMS19GetProcessStatus $parameters) {
    return $this->__soapCall('GIKEMS19GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS19GetSchema $parameters
   * @return GIKEMS19GetSchemaResponse
   */
  public function GIKEMS19GetSchema(GIKEMS19GetSchema $parameters) {
    return $this->__soapCall('GIKEMS19GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS19SetSchema $parameters
   * @return GIKEMS19SetSchemaResponse
   */
  public function GIKEMS19SetSchema(GIKEMS19SetSchema $parameters) {
    return $this->__soapCall('GIKEMS19SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS19Export $parameters
   * @return GIKEMS19ExportResponse
   */
  public function GIKEMS19Export(GIKEMS19Export $parameters) {
    return $this->__soapCall('GIKEMS19Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS19Import $parameters
   * @return GIKEMS19ImportResponse
   */
  public function GIKEMS19Import(GIKEMS19Import $parameters) {
    return $this->__soapCall('GIKEMS19Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS19Submit $parameters
   * @return GIKEMS19SubmitResponse
   */
  public function GIKEMS19Submit(GIKEMS19Submit $parameters) {
    return $this->__soapCall('GIKEMS19Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS21Clear $parameters
   * @return GIKEMS21ClearResponse
   */
  public function GIKEMS21Clear(GIKEMS21Clear $parameters) {
    return $this->__soapCall('GIKEMS21Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS21GetProcessStatus $parameters
   * @return GIKEMS21GetProcessStatusResponse
   */
  public function GIKEMS21GetProcessStatus(GIKEMS21GetProcessStatus $parameters) {
    return $this->__soapCall('GIKEMS21GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS21GetSchema $parameters
   * @return GIKEMS21GetSchemaResponse
   */
  public function GIKEMS21GetSchema(GIKEMS21GetSchema $parameters) {
    return $this->__soapCall('GIKEMS21GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS21SetSchema $parameters
   * @return GIKEMS21SetSchemaResponse
   */
  public function GIKEMS21SetSchema(GIKEMS21SetSchema $parameters) {
    return $this->__soapCall('GIKEMS21SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS21Export $parameters
   * @return GIKEMS21ExportResponse
   */
  public function GIKEMS21Export(GIKEMS21Export $parameters) {
    return $this->__soapCall('GIKEMS21Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS21Import $parameters
   * @return GIKEMS21ImportResponse
   */
  public function GIKEMS21Import(GIKEMS21Import $parameters) {
    return $this->__soapCall('GIKEMS21Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS21Submit $parameters
   * @return GIKEMS21SubmitResponse
   */
  public function GIKEMS21Submit(GIKEMS21Submit $parameters) {
    return $this->__soapCall('GIKEMS21Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS22Clear $parameters
   * @return GIKEMS22ClearResponse
   */
  public function GIKEMS22Clear(GIKEMS22Clear $parameters) {
    return $this->__soapCall('GIKEMS22Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS22GetProcessStatus $parameters
   * @return GIKEMS22GetProcessStatusResponse
   */
  public function GIKEMS22GetProcessStatus(GIKEMS22GetProcessStatus $parameters) {
    return $this->__soapCall('GIKEMS22GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS22GetSchema $parameters
   * @return GIKEMS22GetSchemaResponse
   */
  public function GIKEMS22GetSchema(GIKEMS22GetSchema $parameters) {
    return $this->__soapCall('GIKEMS22GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS22SetSchema $parameters
   * @return GIKEMS22SetSchemaResponse
   */
  public function GIKEMS22SetSchema(GIKEMS22SetSchema $parameters) {
    return $this->__soapCall('GIKEMS22SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS22Export $parameters
   * @return GIKEMS22ExportResponse
   */
  public function GIKEMS22Export(GIKEMS22Export $parameters) {
    return $this->__soapCall('GIKEMS22Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS22Import $parameters
   * @return GIKEMS22ImportResponse
   */
  public function GIKEMS22Import(GIKEMS22Import $parameters) {
    return $this->__soapCall('GIKEMS22Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param GIKEMS22Submit $parameters
   * @return GIKEMS22SubmitResponse
   */
  public function GIKEMS22Submit(GIKEMS22Submit $parameters) {
    return $this->__soapCall('GIKEMS22Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param IN202500Clear $parameters
   * @return IN202500ClearResponse
   */
  public function IN202500Clear(IN202500Clear $parameters) {
    return $this->__soapCall('IN202500Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param IN202500GetProcessStatus $parameters
   * @return IN202500GetProcessStatusResponse
   */
  public function IN202500GetProcessStatus(IN202500GetProcessStatus $parameters) {
    return $this->__soapCall('IN202500GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param IN202500GetSchema $parameters
   * @return IN202500GetSchemaResponse
   */
  public function IN202500GetSchema(IN202500GetSchema $parameters) {
    return $this->__soapCall('IN202500GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param IN202500SetSchema $parameters
   * @return IN202500SetSchemaResponse
   */
  public function IN202500SetSchema(IN202500SetSchema $parameters) {
    return $this->__soapCall('IN202500SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param IN202500Export $parameters
   * @return IN202500ExportResponse
   */
  public function IN202500Export(IN202500Export $parameters) {
    return $this->__soapCall('IN202500Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param IN202500Import $parameters
   * @return IN202500ImportResponse
   */
  public function IN202500Import(IN202500Import $parameters) {
    return $this->__soapCall('IN202500Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param IN202500Submit $parameters
   * @return IN202500SubmitResponse
   */
  public function IN202500Submit(IN202500Submit $parameters) {
    return $this->__soapCall('IN202500Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param KN202500Clear $parameters
   * @return KN202500ClearResponse
   */
  public function KN202500Clear(KN202500Clear $parameters) {
    return $this->__soapCall('KN202500Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param KN202500GetProcessStatus $parameters
   * @return KN202500GetProcessStatusResponse
   */
  public function KN202500GetProcessStatus(KN202500GetProcessStatus $parameters) {
    return $this->__soapCall('KN202500GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param KN202500GetSchema $parameters
   * @return KN202500GetSchemaResponse
   */
  public function KN202500GetSchema(KN202500GetSchema $parameters) {
    return $this->__soapCall('KN202500GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param KN202500SetSchema $parameters
   * @return KN202500SetSchemaResponse
   */
  public function KN202500SetSchema(KN202500SetSchema $parameters) {
    return $this->__soapCall('KN202500SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param KN202500Export $parameters
   * @return KN202500ExportResponse
   */
  public function KN202500Export(KN202500Export $parameters) {
    return $this->__soapCall('KN202500Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param KN202500Import $parameters
   * @return KN202500ImportResponse
   */
  public function KN202500Import(KN202500Import $parameters) {
    return $this->__soapCall('KN202500Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param KN202500Submit $parameters
   * @return KN202500SubmitResponse
   */
  public function KN202500Submit(KN202500Submit $parameters) {
    return $this->__soapCall('KN202500Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param KN505888Clear $parameters
   * @return KN505888ClearResponse
   */
  public function KN505888Clear(KN505888Clear $parameters) {
    return $this->__soapCall('KN505888Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param KN505888GetProcessStatus $parameters
   * @return KN505888GetProcessStatusResponse
   */
  public function KN505888GetProcessStatus(KN505888GetProcessStatus $parameters) {
    return $this->__soapCall('KN505888GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param KN505888GetSchema $parameters
   * @return KN505888GetSchemaResponse
   */
  public function KN505888GetSchema(KN505888GetSchema $parameters) {
    return $this->__soapCall('KN505888GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param KN505888SetSchema $parameters
   * @return KN505888SetSchemaResponse
   */
  public function KN505888SetSchema(KN505888SetSchema $parameters) {
    return $this->__soapCall('KN505888SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param KN505888Export $parameters
   * @return KN505888ExportResponse
   */
  public function KN505888Export(KN505888Export $parameters) {
    return $this->__soapCall('KN505888Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param KN505888Import $parameters
   * @return KN505888ImportResponse
   */
  public function KN505888Import(KN505888Import $parameters) {
    return $this->__soapCall('KN505888Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param KN505888Submit $parameters
   * @return KN505888SubmitResponse
   */
  public function KN505888Submit(KN505888Submit $parameters) {
    return $this->__soapCall('KN505888Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SO301000Clear $parameters
   * @return SO301000ClearResponse
   */
  public function SO301000Clear(SO301000Clear $parameters) {
    return $this->__soapCall('SO301000Clear', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SO301000GetProcessStatus $parameters
   * @return SO301000GetProcessStatusResponse
   */
  public function SO301000GetProcessStatus(SO301000GetProcessStatus $parameters) {
    return $this->__soapCall('SO301000GetProcessStatus', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SO301000GetSchema $parameters
   * @return SO301000GetSchemaResponse
   */
  public function SO301000GetSchema(SO301000GetSchema $parameters) {
    return $this->__soapCall('SO301000GetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SO301000SetSchema $parameters
   * @return SO301000SetSchemaResponse
   */
  public function SO301000SetSchema(SO301000SetSchema $parameters) {
    return $this->__soapCall('SO301000SetSchema', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SO301000Export $parameters
   * @return SO301000ExportResponse
   */
  public function SO301000Export(SO301000Export $parameters) {
    return $this->__soapCall('SO301000Export', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SO301000Import $parameters
   * @return SO301000ImportResponse
   */
  public function SO301000Import(SO301000Import $parameters) {
    return $this->__soapCall('SO301000Import', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param SO301000Submit $parameters
   * @return SO301000SubmitResponse
   */
  public function SO301000Submit(SO301000Submit $parameters) {
    return $this->__soapCall('SO301000Submit', array($parameters),       array(
            'uri' => 'http://www.acumatica.com/generic/',
            'soapaction' => ''
           )
      );
  }

}

?>
