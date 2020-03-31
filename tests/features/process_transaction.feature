Feature:
  In order to transfer money
  As a user
  I want my payment transaction to be processed

  Background:
    Given the system processed confirmed transactions

  Scenario: A confirmed eur transaction is processed successfully
    When I make request "GET" "/api/transactions/3"
    Then the response status code should be 200
    And the response JSON should be a single object
    And the response JSON should have "status" field with value "completed"
    And the response JSON should have "details" field with value "Transaction number o"

  Scenario: A confirmed non-eur transaction is processed successfully
    When I make request "GET" "/api/transactions/4"
    Then the response status code should be 200
    And the response JSON should be a single object
    And the response JSON should have "status" field with value "completed"
    And the response JSON should have "details" field with value "Transaction number one 804318771"

  Scenario: An unconfirmed transaction is not processed
    When I make request "GET" "/api/transactions/2"
    Then the response status code should be 200
    And the response JSON should be a single object
    And the response JSON should have "status" field with value "created"
    And the response JSON should have "details" field with value "Transaction number one"

