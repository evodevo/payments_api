Feature:
  In order for my payments to be secure
  As a user
  I want to be able to confirm payments

#  Background:
#    Given I make request "GET" "/api/transactions/1"
#    And the response JSON should have "status" field with value "created"

  Scenario: User fails to confirm transaction
    When I make request "PUT" "/api/transactions/1/confirmation" with following JSON content:
    """
    {
      "code": 123
    }
    """
    Then the response status code should be 422
    And the response JSON should be a single object
    And the response JSON should have "code" field with value 422
    And the response JSON should have "message" field

  Scenario: User confirms transaction successfully
    When I make request "PUT" "/api/transactions/1/confirmation" with following JSON content:
    """
    {
      "code": 111
    }
    """
    Then the response status code should be 202
    And the response JSON should be a single object
    And the response JSON should have "success" field with value true

  Scenario: User tries to confirm already confirmed transaction
    When I make request "PUT" "/api/transactions/1/confirmation" with following JSON content:
    """
    {
      "code": 111
    }
    """
    Then the response status code should be 409
    And the response JSON should be a single object
    And the response JSON should have "code" field with value 409
    And the response JSON should have "message" field



