<?php
/**
 * @package nxcCaptcha
 * @class   nxcCaptchaType
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    13 Sep 2011
 **/

class nxcCaptchaType extends eZDataType
{
	const DATA_TYPE_STRING  = 'nxccaptcha';
	const TYPE_ALPHABETIC   = 1;
	const TYPE_NUMERIC      = 2;
	const TYPE_ALPHANUMERIC = 3;

	public static $definition = array(
		'length' => array(
			'field'   => 'data_int1',
			'min'     => 3,
			'max'     => 12,
			'default' => 4
		),
		'type' => array(
			'field'           => 'data_int2',
			'default'         => 1,
			'possible_values' => array()
		),
		'width' => array(
			'field'   => 'data_int3',
			'min'     => 100,
			'max'     => 500,
			'default' => 200
		),
		'height' => array(
			'field'   => 'data_int4',
			'min'     => 30,
			'max'     => 200,
			'default' => 50
		),
		'noise_level' => array(
			'field'   => 'data_float1',
			'min'     => 1,
			'max'     => 100,
			'default' => 40
		),
		'text_tilt_angle' => array(
			'field'   => 'data_float2',
			'min'     => 1,
			'max'     => 60,
			'default' => 15
		),
		'characters_color' => array(
			'field'   => array(
				'field' => 'data_text5',
				'index' => 'chracters'
			),
			'default' => 'ffffff'
		),
		'background_color' => array(
			'field'   => array(
				'field' => 'data_text5',
				'index' => 'background'
			),
			'default' => '0063ff'
		),
		'noise_color' => array(
			'field'   => array(
				'field' => 'data_text5',
				'index' => 'noise'
			),
			'default' => '42ccff'
		),
		'skip_user_ids' => array(
			'field'   => 'data_text1',
			'default' => 14
		),
		'skip_role_ids' => array(
			'field'           => 'data_text2',
			'default'         => 2,
			'possible_values' => array()
		),
		'exclude_characters' => array(
			'field'    => 'data_text3',
			'default' => '0,o,O,l,I,i'
		),
	);

	public function __construct() {
		$this->eZDataType(
			self::DATA_TYPE_STRING,
			ezpI18n::tr( 'extension/nxc_captcha', 'NXC Captcha' ),
			array(
				'serialize_supported'  => true,
				'object_serialize_map' => array(
					'data_text' => 'captcha_code',
					'data_int'  => 'checked'
				)
			)
		);

		if( count( self::$definition['type']['possible_values'] ) === 0 ) {
			self::$definition['type']['possible_values'] = array(
				self::TYPE_ALPHABETIC   => ezpI18n::tr( 'extension/nxc_captcha', 'Alphabetic' ),
				self::TYPE_NUMERIC      => ezpI18n::tr( 'extension/nxc_captcha', 'Numeric' ),
				self::TYPE_ALPHANUMERIC => ezpI18n::tr( 'extension/nxc_captcha', 'Alphanumeric' )
			);
		}
		if( count( self::$definition['skip_role_ids']['possible_values'] ) === 0 ) {
			$roles = eZRole::fetchList();
			foreach( $roles as $role ) {
				self::$definition['skip_role_ids']['possible_values'][ $role->attribute( 'id' ) ] = $role->attribute( 'name' );
			}
		}
	}

	public function initializeClassAttribute( $classAttribute ) {
		if( $classAttribute->attribute( 'id' ) === null ) {
			$fields = array();
			foreach( self::$definition as $option => $info ) {
				if( is_array( $info['field'] ) ) {
					$fieldDef = $info['field'];
					if( isset( $fields[ $fieldDef['field'] ] ) === false ) {
						$fields[ $fieldDef['field'] ] = array();
					}
					$fields[ $fieldDef['field'] ][ $fieldDef['index'] ] = $info['default'];
				} else {
					$fields[ $info['field'] ] = $info['default'];
				}
			}
			self::setClassAttributeFields( $classAttribute, $fields );
		}
	}

	public function fetchClassAttributeHTTPInput( $http, $base, $classAttribute ) {
		$customAction =
			$http->hasPostVariable( 'StoreButton' ) === false
			&& $http->hasPostVariable( 'ApplyButton' ) === false;
		$httpVariable = 'nxc_captcha_' . $classAttribute->attribute( 'id' ) . '_options';
		if(
			$http->hasPostVariable( $httpVariable )
			&& $customAction === false
		) {
			$input = $http->postVariable( $httpVariable );
			if( isset( $input['skip_role_ids'] ) === false ) {
				$input['skip_role_ids'] = array();
			}

			$fileds = array();
			foreach( self::$definition as $option => $info ) {
				if( isset( $input[ $option ] ) ) {
					$value = $input[ $option ];
					if( is_array( $value ) ) {
						$value = implode( ',', $value );
					}

					if( is_array( $info['field'] ) ) {
						$fieldDef = $info['field'];
						if( isset( $fields[ $fieldDef['field'] ] ) === false ) {
							$fields[ $fieldDef['field'] ] = array();
						}
						$fields[ $fieldDef['field'] ][ $fieldDef['index'] ] = $value;
					} else {
						$fields[ $info['field'] ] = $value;
					}
				}
			}

			self::setClassAttributeFields( $classAttribute, $fields );
		}
	}

	public function validateClassAttributeHTTPInput( $http, $base, $classAttribute ) {
		$httpVariable = 'nxc_captcha_' . $classAttribute->attribute( 'id' ) . '_options';
		if( $http->hasPostVariable( $httpVariable ) ) {
			$input = $http->postVariable( $httpVariable );
			foreach( self::$definition as $option => $info ) {
				if( isset( $input[ $option ] ) ) {
					$value = $input[ $option ];
					if(
						isset( $info['max'] )
						&& isset( $info['min'] )
					) {
						if(
							(int) $value > (int) $info['max']
							|| (int) $value < (int) $info['min']
						) {
							return eZInputValidator::STATE_INVALID;
						}
					} elseif( isset( $info['allowed'] ) ) {
						if( is_array( $value ) ) {
							foreach( $value as $id ) {
								if( isset( $info['allowed'][$id] ) === false ) {
									return eZInputValidator::STATE_INVALID;
								}
							}
						} else {
							if( isset( $info['allowed'][$value] ) === false ) {
								return eZInputValidator::STATE_INVALID;
							}
						}
					} elseif( strpos( $option, '_color' ) !== false ) {
						if( self::validateColor( $value ) === false ) {
							return eZInputValidator::STATE_INVALID;
						}
					} elseif( $option == 'skip_user_ids' ) {
						foreach( $value as $userID ) {
							$object = eZContentObject::fetch( $userID );
							if( $object instanceof eZContentObject === false ) {
								return eZInputValidator::STATE_INVALID;
							}
						}
					}
				} elseif(
					in_array( $option, array( 'skip_user_ids', 'skip_role_ids' ) ) === false
				) {
					return eZInputValidator::STATE_INVALID;
				}
			}
		}

		return eZInputValidator::STATE_ACCEPTED;
	}

	private static function validateColor( $value ) {
		if( strcspn( strtolower( $value ), '0123456789abcdef' ) ) {
			return false;
		}

		return ( strlen( $value ) === 6 ) ? true : false ;
	}

	public function classAttributeContent( $classAttribute ) {
		$fields = array();
		foreach( self::$definition as $option => $info ) {
			if( is_array( $info['field'] ) ) {
				$field = $info['field']['field'];
				$value = $classAttribute->attribute( $field );
				$value = unserialize( $value );
			} else {
				$field = $info['field'];
				$value = $classAttribute->attribute( $field );
			}
			$fields[ $field ] = $value;
		}

		$options = self::$definition;
		foreach( $options as $option => $info ) {
			$value = null;
			if( is_array( $info['field'] ) ) {
				if( isset( $fields[ $info['field']['field'] ] ) ) {
					$values = $fields[ $info['field']['field'] ];
					if( isset( $values[ $info['field']['index'] ] ) ) {
						$value = $values[ $info['field']['index'] ];
					}
				}
			} else {
				if( isset( $fields[ $info['field'] ] ) ) {
					$value = $fields[ $info['field'] ];
				}
			}
			$options[ $option ]['value'] = $value;
		}
		$options['skip_user_ids']['value'] = explode( ',', $options['skip_user_ids']['value'] );
		$options['skip_role_ids']['value'] = explode( ',', $options['skip_role_ids']['value'] );

		return $options;
	}

	public function customClassAttributeHTTPAction( $http, $action, $classAttribute ) {
		$object = false;

		preg_match( "#^(.*)_([0-9]+)$#", $action, $matches );
		if( count( $matches ) === 3 ) {
			$action = $matches[1];
			$object = eZContentObject::fetch( $matches[2] );
		}

		$skipUserIDs = $classAttribute->attribute( self::$definition['skip_user_ids']['field'] );
		$skipUserIDs = explode( ',', $skipUserIDs );

		switch( $action ) {
			case 'browse_user': {
				$ini = eZINI::instance();
				$id  = $classAttribute->attribute( 'id' );
				$uri = eZURI::instance( eZSys::requestURI() );

				$userClass = eZContentClass::fetch( $ini->variable( 'UserSettings', 'UserClassID' ) );
				if( $userClass instanceof eZContentClass === false ) {
					break;
				}

				$browseParameters = array(
					'action_name'          => 'AddRelatedObjectToDataType',
					'browse_custom_action' => array(
						'name'  => 'CustomActionButton[' . $id . '_add_user]',
						'value' => 'add_user'
					),
					'persistent_data'      => array( 'ContentClassHasInput' => 0 ),
					'from_page'            => $uri->originalURIString(),
					'start_node'           => 5,
					'class_array'          => array( $userClass->attribute( 'identifier' ) )
				);
				$module    = null;
				$browseURL = eZContentBrowse::browse( $browseParameters, $module );
				eZURI::transformURI( $browseURL );

				$http->redirect( $browseURL );
				break;
			}
			case 'add_user': {
				if(
					$http->hasPostVariable( 'SelectedObjectIDArray' )
					&& $http->hasPostVariable( 'BrowseCancelButton' ) === false
				) {
					$selectedObjectIDs = $http->postVariable( 'SelectedObjectIDArray' );
					$selectedObjectID  = $selectedObjectIDs[0];
				}

				if( in_array( $selectedObjectID, $skipUserIDs ) === false ) {
					$skipUserIDs[] = $selectedObjectID;
					$classAttribute->setAttribute(
						self::$definition['skip_user_ids']['field'],
						implode( ',', $skipUserIDs )
					);
				}

				break;
			}
			case 'remove_skip_user': {
				if( $object instanceof eZContentObject ) {
					foreach( $skipUserIDs as $key => $userID ) {
						if( $userID == $object->attribute( 'id' ) ) {
							unset( $skipUserIDs[ $key ] );
							break;
						}
					}
					$classAttribute->setAttribute(
						self::$definition['skip_user_ids']['field'],
						implode( ',', $skipUserIDs )
					);
				}
				break;
			}
		}
	}

	public function serializeContentClassAttribute(
		$classAttribute, $attributeNode, $attributeParametersNode
	) {
		$dom = $attributeParametersNode->ownerDocument;
		$content = $classAttribute->attribute( 'content' );

		foreach( $content as $option => $info ) {
			$value = is_array( $info['value'] )
				? implode( ',', $info['value'] )
				: $info['value'];
			$node  = $dom->createElement( $option, $value );
			$attributeParametersNode->appendChild( $node );
		}
	}

	public function unserializeContentClassAttribute(
		$classAttribute, $attributeNode, $attributeParametersNode
	) {
		$fileds = array();
		foreach( self::$definition as $option => $info ) {
			$nodes = $attributeParametersNode->getElementsByTagName( $option );
			if( (int) $nodes->length > 0 ) {
				$value = $nodes->item( 0 )->textContent;

				if( is_array( $info['field'] ) ) {
					$fieldDef = $info['field'];
					if( isset( $fields[ $fieldDef['field'] ] ) === false ) {
						$fields[ $fieldDef['field'] ] = array();
					}
					$fields[ $fieldDef['field'] ][ $fieldDef['index'] ] = $value;
				} else {
					$fields[ $info['field'] ] = $value;
				}
			}
		}

		self::setClassAttributeFields( $classAttribute, $fields );
	}

	public function objectAttributeContent( $attribute ) {
		return array(
			'checked'      => (bool) $attribute->attribute( 'data_int' ),
			'captcha_code' => $attribute->attribute( 'data_text' )
		);
	}

	public function initializeObjectAttribute( $attribute, $currentVersion, $originalAttribute ) {
		$checkSkipLists = self::checkSkipLists( $attribute );

		if(
			$checkSkipLists === true
			|| (bool) $attribute->attribute( 'is_information_collector' ) === true
		) {
			$attribute->setAttribute( 'data_int', 1 );
			$attribute->setAttribute( 'data_text', 'skipped' );
		} else {
			$attribute->setAttribute( 'data_int', 0 );
			$attribute->setAttribute( 'data_text', null );
			$attribute->store();
		}
	}

	public function fetchObjectAttributeHTTPInput( $http, $base, $attribute ) {
		$pVariable = 'nxc_captcha_' . $attribute->attribute( 'id' );
		if( $http->hasPostVariable( $pVariable ) ) {
			$attribute->setAttribute( 'data_text', $http->postVariable( $pVariable ) );
			return true;
		}
		return false;
	}

	public function validateObjectAttributeHTTPInput( $http, $base, $attribute ) {
		if( (bool) $attribute->attribute( 'data_int' ) === true ) {
			return eZInputValidator::STATE_ACCEPTED;
		}

		return self::checkCaptchaCode(
			$http,
			'nxc_captcha_' . $attribute->attribute( 'id' ),
			'nxc_captcha_object_attribute_' . $attribute->attribute( 'id' ),
			$attribute
		);
	}

	public function hasObjectAttributeContent( $attribute ) {
		return (bool) strlen( trim( $attribute->attribute( 'data_text' ) ) );
	}

	public function isInformationCollector() {
		return true;
	}

	public function fetchCollectionAttributeHTTPInput(
		$collection, $collectionAttribute, $http, $base, $attribute
	) {
		if( self::checkSkipLists( $attribute ) === true ) {
			$collectionAttribute->setAttribute( 'data_int', 1 );
			$collectionAttribute->setAttribute( 'data_text', 'skip' );
			return true;
		} else {
			$pVariable = 'nxc_captcha_' . $attribute->attribute( 'id' );
			if( $http->hasPostVariable( $pVariable ) ) {
				$collectionAttribute->setAttribute(
					'data_text',
					$http->postVariable( $pVariable )
				);

				$check = self::checkCaptchaCode(
					$http,
					'nxc_captcha_' . $attribute->attribute( 'id' ),
					'nxc_captcha_collection_attribute_' . $attribute->attribute( 'id' ),
					$attribute,
					true
				);
				if( $check === eZInputValidator::STATE_ACCEPTED ) {
					$collectionAttribute->setAttribute( 'data_int', 1 );
				}

				return true;
			}
		}
		return false;
	}

	public function validateCollectionAttributeHTTPInput( $http, $base, $attribute ) {
		if( self::checkSkipLists( $attribute ) === true ) {
			return eZInputValidator::STATE_ACCEPTED;
		}

		return self::checkCaptchaCode(
			$http,
			'nxc_captcha_' . $attribute->attribute( 'id' ),
			'nxc_captcha_collection_attribute_' . $attribute->attribute( 'id' ),
			$attribute,
			true
		);
	}

	private static function checkSkipLists( $attribute ) {
		$classContet = $attribute->attribute( 'contentclass_attribute' )->attribute( 'content' );
		$skipUserIDs = $classContet['skip_user_ids']['value'];
		$skipRoleIDs = $classContet['skip_role_ids']['value'];

		$user = eZUser::currentUser();
		if(
			is_array( $skipUserIDs )
			&& in_array( $user->attribute( 'contentobject_id' ), $skipUserIDs )
		) {
			return true;
		}

		$userRoleIDs = $user->attribute( 'role_id_list' );
		if(
			is_array( $skipRoleIDs )
			&& count( $skipRoleIDs ) > 0
			&& count( $userRoleIDs ) > 0
			&& count( array_intersect( $skipRoleIDs, $userRoleIDs ) ) > 0
		) {
			return true;
		}

		return false;
	}

	private static function checkCaptchaCode(
		$http,
		$pVariable,
		$sVariable,
		$attribute,
		$collecting = false
	) {
		if( $http->hasPostVariable( $pVariable ) ) {
			$pCaptcha = strtolower( $http->postVariable( $pVariable ) );
			$sCaptcha = strtolower( $http->sessionVariable( $sVariable ) );

			eZDebug::writeDebug( $sCaptcha, 'Current captcha value' );
			if(
				strlen( $sCaptcha ) > 0
				&& $pCaptcha == $sCaptcha
			) {
				if( $collecting === false ) {
					$attribute->setAttribute( 'data_int', 1 );
					$attribute->setAttribute( 'data_text', $http->postVariable( $pVariable ) );
					$attribute->store();
					$http->removeSessionVariable( $sVariable );
				}

				return eZInputValidator::STATE_ACCEPTED;
			} else {
				$attribute->setValidationError(
					ezpI18n::tr( 'extension/nxc_captcha', 'Secure code is not correct.' )
				);
				return eZInputValidator::STATE_INVALID;
			}
		}

		$attribute->setValidationError(
			ezpI18n::tr( 'extension/nxc_captcha', 'Secure code is missing.' )
		);
		return eZInputValidator::STATE_INVALID;
	}

	public function toString( $attribute ) {
		return $attribute->attribute( 'data_text' ) . '|' .  $attribute->attribute( 'data_int' );
	}

	public function fromString( $attribute, $string ) {
		$data = explode( '|', $string );
		$attribute->setAttribute( 'data_text', $data[0] );
		if( isset( $data[1] ) ) {
			$attribute->setAttribute( 'data_int', $data[1] );
		}
	}

	public function supportsBatchInitializeObjectAttribute() {
		return false;
	}

	public function title( $attribute, $name = null ) {
		return null;
	}

	public function isIndexable() {
		return false;
	}

	public function sortKeyType() {
		return false;
	}

	public function metaData( $attribute ) {
		return null;
	}

	public function diff( $old, $new, $options = false ) {
		return null;
	}

	private static function setClassAttributeFields( $classAttribute, $fields ) {
		foreach( $fields as $field => $value ) {
			if( $classAttribute->hasAttribute( $field ) ) {
				$classAttribute->setAttribute(
					$field,
					is_array( $value ) ? serialize( $value ) : $value
				);
			}
		}
	}
}

eZDataType::register( nxcCaptchaType::DATA_TYPE_STRING, 'nxcCaptchaType' );
?>
