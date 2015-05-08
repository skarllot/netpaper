# API documentation

## Version 1

### /v1/languages

#### Data Structure

	struct {
		id: int64
		code: string
		name: string
	} 
	
#### GET

Get available languages for current instance.

Authentication not required.

### /v1/install

#### Data Structure

	struct {
		id: int64
		user: string
		password: string
		email: string, null
		name: string
		isAdmin: boolean
		isLdap: boolean
		version: int64
		language: struct language
	}
	
#### GET

Returns true whether first configuration is already executed. Otherwise returns false.

#### POST

Receive a user data structure to create first administrative user.