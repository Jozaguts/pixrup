Property Record by Id

# Property Record by Id

Returns a single property record matching the specified id.

<HTMLBlock>
  {`
  &nbsp;
  `}
</HTMLBlock>

This endpoint returns a single property record matching the specified internal id, if it exists. Property ids can be obtained via the [`/properties`](https://developers.rentcast.io/reference/property-data), [`/avm`](https://developers.rentcast.io/reference/property-valuation) or [`/listings`](https://developers.rentcast.io/reference/property-listings) endpoints, or cached in your application from prior requests.

The returned property record will include data for a specific property, including its structural attributes, features, tax assessments, tax amounts, sale history, and owner details. View the full [property data schema](https://developers.rentcast.io/reference/property-data-schema) to learn more about the response fields.

<Callout icon="📘" theme="info">
  Property ids used by our API are case-sensitive and should be provided in the same format as the `id` field contained in the responses from our API.
</Callout>

<Callout icon="📘" theme="info">
  Data availability of specific property record fields may vary by county and state. Our API will always return all available fields and data for each property.
</Callout>

<HTMLBlock>
  {`
  &nbsp;
  `}
</HTMLBlock>

# OpenAPI definition

```json
{
  "openapi": "3.1.0",
  "info": {
    "title": "RentCast API",
    "version": "1.0"
  },
  "servers": [
    {
      "url": "https://api.rentcast.io/v1"
    }
  ],
  "components": {
    "securitySchemes": {
      "sec0": {
        "type": "apiKey",
        "in": "header",
        "name": "X-Api-Key"
      }
    }
  },
  "security": [
    {
      "sec0": []
    }
  ],
  "paths": {
    "/properties/{id}": {
      "get": {
        "summary": "Property Record by Id",
        "description": "Returns a single property record matching the specified id.",
        "operationId": "property-record-by-id",
        "parameters": [
          {
            "name": "id",
            "in": "path",
            "description": "The id of the property record to return",
            "schema": {
              "type": "string",
              "default": "5500-Grand-Lake-Dr,-San-Antonio,-TX-78244"
            },
            "required": true
          }
        ],
        "responses": {
          "200": {
            "description": "Success",
            "content": {
              "application/json": {
                "examples": {
                  "Success": {
                    "summary": "Success",
                    "value": {
                      "id": "5500-Grand-Lake-Dr,-San-Antonio,-TX-78244",
                      "formattedAddress": "5500 Grand Lake Dr, San Antonio, TX 78244",
                      "addressLine1": "5500 Grand Lake Dr",
                      "addressLine2": null,
                      "city": "San Antonio",
                      "state": "TX",
                      "stateFips": "48",
                      "zipCode": "78244",
                      "county": "Bexar",
                      "countyFips": "029",
                      "latitude": 29.475962,
                      "longitude": -98.351442,
                      "propertyType": "Single Family",
                      "bedrooms": 3,
                      "bathrooms": 2,
                      "squareFootage": 1878,
                      "lotSize": 8850,
                      "yearBuilt": 1973,
                      "assessorID": "05076-103-0500",
                      "legalDescription": "CB 5076A BLK 3 LOT 50",
                      "subdivision": "WOODLAKE",
                      "zoning": "RH",
                      "lastSaleDate": "2024-11-18T00:00:00.000Z",
                      "lastSalePrice": 270000,
                      "hoa": {
                        "fee": 175
                      },
                      "features": {
                        "architectureType": "Contemporary",
                        "cooling": true,
                        "coolingType": "Central",
                        "exteriorType": "Wood",
                        "fireplace": true,
                        "fireplaceType": "Masonry",
                        "floorCount": 1,
                        "foundationType": "Slab / Mat / Raft",
                        "garage": true,
                        "garageSpaces": 2,
                        "garageType": "Garage",
                        "heating": true,
                        "heatingType": "Forced Air",
                        "pool": true,
                        "poolType": "Concrete",
                        "roofType": "Asphalt",
                        "roomCount": 5,
                        "unitCount": 1,
                        "viewType": "City"
                      },
                      "taxAssessments": {
                        "2020": {
                          "year": 2020,
                          "value": 142610,
                          "land": 23450,
                          "improvements": 119160
                        },
                        "2021": {
                          "year": 2021,
                          "value": 163440,
                          "land": 45050,
                          "improvements": 118390
                        },
                        "2022": {
                          "year": 2022,
                          "value": 197600,
                          "land": 49560,
                          "improvements": 148040
                        },
                        "2023": {
                          "year": 2023,
                          "value": 225790,
                          "land": 59380,
                          "improvements": 166410
                        },
                        "2024": {
                          "year": 2024,
                          "value": 216513,
                          "land": 59380,
                          "improvements": 157133
                        }
                      },
                      "propertyTaxes": {
                        "2020": {
                          "year": 2020,
                          "total": 3023
                        },
                        "2021": {
                          "year": 2021,
                          "total": 3455
                        },
                        "2022": {
                          "year": 2022,
                          "total": 4077
                        },
                        "2023": {
                          "year": 2023,
                          "total": 4201
                        },
                        "2024": {
                          "year": 2024,
                          "total": 4065
                        }
                      },
                      "history": {
                        "2017-10-19": {
                          "event": "Sale",
                          "date": "2017-10-19T00:00:00.000Z",
                          "price": 185000
                        },
                        "2024-11-18": {
                          "event": "Sale",
                          "date": "2024-11-18T00:00:00.000Z",
                          "price": 270000
                        }
                      },
                      "owner": {
                        "names": [
                          "Rolando Villarreal"
                        ],
                        "type": "Individual",
                        "mailingAddress": {
                          "id": "5500-Grand-Lake-Dr,-San-Antonio,-TX-78244",
                          "formattedAddress": "5500 Grand Lake Dr, San Antonio, TX 78244",
                          "addressLine1": "5500 Grand Lake Dr",
                          "addressLine2": null,
                          "city": "San Antonio",
                          "state": "TX",
                          "stateFips": "48",
                          "zipCode": "78244"
                        }
                      },
                      "ownerOccupied": true
                    }
                  }
                }
              }
            }
          },
          "401": {
            "description": "Auth Error",
            "content": {
              "application/json": {
                "examples": {
                  "Auth Error": {
                    "value": {
                      "status": 401,
                      "error": "auth/api-key-invalid",
                      "message": "No API key provided in request. An API key must be provided in the 'X-Api-Key' header"
                    },
                    "summary": "Auth Error"
                  }
                },
                "schema": {
                  "properties": {
                    "status": {
                      "type": "integer"
                    },
                    "error": {
                      "type": "string"
                    },
                    "message": {
                      "type": "string"
                    }
                  },
                  "type": "object"
                }
              }
            }
          }
        },
        "deprecated": false,
        "security": [
          {
            "sec0": []
          }
        ]
      }
    }
  },
  "x-readme": {
    "headers": [],
    "explorer-enabled": true,
    "proxy-enabled": true
  },
  "x-readme-fauxas": true
}
```

# OpenAPI definition
```json
{
  "_id": "/branches/1.0/apis/rentcast-api.json",
  "openapi": "3.1.0",
  "info": {
    "title": "RentCast API",
    "version": "1.0"
  },
  "servers": [
    {
      "url": "https://api.rentcast.io/v1"
    }
  ],
  "components": {
    "securitySchemes": {
      "sec0": {
        "type": "apiKey",
        "in": "header",
        "name": "X-Api-Key"
      }
    }
  },
  "security": [
    {
      "sec0": []
    }
  ],
  "paths": {
    "/properties/{id}": {
      "get": {
        "summary": "Property Record by Id",
        "description": "Returns a single property record matching the specified id.",
        "operationId": "property-record-by-id",
        "parameters": [
          {
            "name": "id",
            "in": "path",
            "description": "The id of the property record to return",
            "schema": {
              "type": "string",
              "default": "5500-Grand-Lake-Dr,-San-Antonio,-TX-78244"
            },
            "required": true
          }
        ],
        "responses": {
          "200": {
            "description": "Success",
            "content": {
              "application/json": {
                "examples": {
                  "Success": {
                    "summary": "Success",
                    "value": {
                      "id": "5500-Grand-Lake-Dr,-San-Antonio,-TX-78244",
                      "formattedAddress": "5500 Grand Lake Dr, San Antonio, TX 78244",
                      "addressLine1": "5500 Grand Lake Dr",
                      "addressLine2": null,
                      "city": "San Antonio",
                      "state": "TX",
                      "stateFips": "48",
                      "zipCode": "78244",
                      "county": "Bexar",
                      "countyFips": "029",
                      "latitude": 29.475962,
                      "longitude": -98.351442,
                      "propertyType": "Single Family",
                      "bedrooms": 3,
                      "bathrooms": 2,
                      "squareFootage": 1878,
                      "lotSize": 8850,
                      "yearBuilt": 1973,
                      "assessorID": "05076-103-0500",
                      "legalDescription": "CB 5076A BLK 3 LOT 50",
                      "subdivision": "WOODLAKE",
                      "zoning": "RH",
                      "lastSaleDate": "2024-11-18T00:00:00.000Z",
                      "lastSalePrice": 270000,
                      "hoa": {
                        "fee": 175
                      },
                      "features": {
                        "architectureType": "Contemporary",
                        "cooling": true,
                        "coolingType": "Central",
                        "exteriorType": "Wood",
                        "fireplace": true,
                        "fireplaceType": "Masonry",
                        "floorCount": 1,
                        "foundationType": "Slab / Mat / Raft",
                        "garage": true,
                        "garageSpaces": 2,
                        "garageType": "Garage",
                        "heating": true,
                        "heatingType": "Forced Air",
                        "pool": true,
                        "poolType": "Concrete",
                        "roofType": "Asphalt",
                        "roomCount": 5,
                        "unitCount": 1,
                        "viewType": "City"
                      },
                      "taxAssessments": {
                        "2020": {
                          "year": 2020,
                          "value": 142610,
                          "land": 23450,
                          "improvements": 119160
                        },
                        "2021": {
                          "year": 2021,
                          "value": 163440,
                          "land": 45050,
                          "improvements": 118390
                        },
                        "2022": {
                          "year": 2022,
                          "value": 197600,
                          "land": 49560,
                          "improvements": 148040
                        },
                        "2023": {
                          "year": 2023,
                          "value": 225790,
                          "land": 59380,
                          "improvements": 166410
                        },
                        "2024": {
                          "year": 2024,
                          "value": 216513,
                          "land": 59380,
                          "improvements": 157133
                        }
                      },
                      "propertyTaxes": {
                        "2020": {
                          "year": 2020,
                          "total": 3023
                        },
                        "2021": {
                          "year": 2021,
                          "total": 3455
                        },
                        "2022": {
                          "year": 2022,
                          "total": 4077
                        },
                        "2023": {
                          "year": 2023,
                          "total": 4201
                        },
                        "2024": {
                          "year": 2024,
                          "total": 4065
                        }
                      },
                      "history": {
                        "2017-10-19": {
                          "event": "Sale",
                          "date": "2017-10-19T00:00:00.000Z",
                          "price": 185000
                        },
                        "2024-11-18": {
                          "event": "Sale",
                          "date": "2024-11-18T00:00:00.000Z",
                          "price": 270000
                        }
                      },
                      "owner": {
                        "names": [
                          "Rolando Villarreal"
                        ],
                        "type": "Individual",
                        "mailingAddress": {
                          "id": "5500-Grand-Lake-Dr,-San-Antonio,-TX-78244",
                          "formattedAddress": "5500 Grand Lake Dr, San Antonio, TX 78244",
                          "addressLine1": "5500 Grand Lake Dr",
                          "addressLine2": null,
                          "city": "San Antonio",
                          "state": "TX",
                          "stateFips": "48",
                          "zipCode": "78244"
                        }
                      },
                      "ownerOccupied": true
                    }
                  }
                }
              }
            }
          },
          "401": {
            "description": "Auth Error",
            "content": {
              "application/json": {
                "examples": {
                  "Auth Error": {
                    "value": {
                      "status": 401,
                      "error": "auth/api-key-invalid",
                      "message": "No API key provided in request. An API key must be provided in the 'X-Api-Key' header"
                    },
                    "summary": "Auth Error"
                  }
                },
                "schema": {
                  "properties": {
                    "status": {
                      "type": "integer"
                    },
                    "error": {
                      "type": "string"
                    },
                    "message": {
                      "type": "string"
                    }
                  },
                  "type": "object"
                }
              }
            }
          }
        },
        "deprecated": false,
        "security": [
          {
            "sec0": []
          }
        ]
      }
    }
  },
  "x-readme": {
    "headers": [],
    "explorer-enabled": true,
    "proxy-enabled": true
  },
  "x-readme-fauxas": true
}
```