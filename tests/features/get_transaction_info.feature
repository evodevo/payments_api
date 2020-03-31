Feature:
  In order to know the status of my transaction
  As a user
  I want to be able to request transaction info

  Scenario: User requests transaction info
    When I make request "GET" "/api/transactions/5"
    Then the response status code should be 200
    And the response JSON should be a single object
    And the response JSON should have "transaction_id" field with value 5
    And the response JSON should have "details" field with value "Transaction number one"
    And the response JSON should have "recipient_account" field with value "12345"
    And the response JSON should have "recipient_name" field with value "John Doe"
    And the response JSON should have "amount" field with value 20
    And the response JSON should have "currency" field with value "eur"
    And the response JSON should have "fee" field with value 2
    And the response JSON should have "status" field with value "completed"

  Scenario: User tries to request non-existent transaction
    When I make request "GET" "/api/transactions/9999999"
    Then the response status code should be 404
    And the response JSON should be a single object
    And the response JSON should have "code" field with value 404
    And the response JSON should have "message" field