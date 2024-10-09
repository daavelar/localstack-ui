#!/bin/bash

# Create SNS topics
SHIPMENT_TOPIC="envios_shipment"
awslocal --endpoint-url http://localhost:4566 sns create-topic --name "$SHIPMENT_TOPIC"

VOLUME_TOPIC="envios_volume"
awslocal --endpoint-url http://localhost:4566 sns create-topic --name "$VOLUME_TOPIC"

TRACKING_EVENTS_TOPIC="envios_tracking_events"
awslocal --endpoint-url http://localhost:4566 sns create-topic --name "$TRACKING_EVENTS_TOPIC"

FINANCIAL_REQUEST_TOPIC="envios_financial_request"
awslocal --endpoint-url http://localhost:4566 sns create-topic --name "$FINANCIAL_REQUEST_TOPIC"

PAX_DELIVERIES_TOPIC="pax__deliveries"
awslocal --endpoint-url http://localhost:4566 sns create-topic --name "$PAX_DELIVERIES_TOPIC"

TRACKING_REQUESTED_SNS_TOPIC="tracking_update__requested"
awslocal --endpoint-url http://localhost:4566 sns create-topic --name "$TRACKING_REQUESTED_SNS_TOPIC"

CARRIER_TRACKING_CREATED_TOPIC="carrier_tracking__created"
awslocal --endpoint-url http://localhost:4566 sns create-topic --name "$CARRIER_TRACKING_CREATED_TOPIC"

# Create SQS queues
COLLECTION_SYNC_VOLUMES_QUEUE="envios_collection_sync_volumes__envios_volume"
awslocal --endpoint-url http://localhost:4566 sqs create-queue --queue-name "$COLLECTION_SYNC_VOLUMES_QUEUE"

TRACKING_NOTIFICATION_QUEUE="envios_tracking_notification__envios_tracking_event"
awslocal --endpoint-url http://localhost:4566 sqs create-queue --queue-name "$TRACKING_NOTIFICATION_QUEUE"

CONTRACTING_FULFILLMENT_UPDATE_QUEUE="contracting_fulfillment_update__envios_shipment"
awslocal --endpoint-url http://localhost:4566 sqs create-queue --queue-name "$CONTRACTING_FULFILLMENT_UPDATE_QUEUE"

CARRIER_TRACKING_SERVICE_QUEUE="carrier_tracking_service__carrier_tracking__created"
awslocal --endpoint-url http://localhost:4566 sqs create-queue --queue-name "$CARRIER_TRACKING_SERVICE_QUEUE"

CARRIER_TRACKING_SERVICE_UPDATE_REQUEST_QUEUE="carrier_tracking_service__tracking_update__requested"
awslocal --endpoint-url http://localhost:4566 sqs create-queue --queue-name "$CARRIER_TRACKING_SERVICE_UPDATE_REQUEST_QUEUE"

# Create Subscriptions
awslocal --endpoint-url http://localhost:4566 sns subscribe --topic-arn "arn:aws:sns:us-east-1:000000000000:$SHIPMENT_TOPIC" --protocol sqs --notification-endpoint "arn:aws:sqs:us-east-1:000000000000:$CONTRACTING_FULFILLMENT_UPDATE_QUEUE"

awslocal --endpoint-url http://localhost:4566 sns subscribe --topic-arn "arn:aws:sns:us-east-1:000000000000:$VOLUME_TOPIC" --protocol sqs --notification-endpoint "arn:aws:sqs:us-east-1:000000000000:$COLLECTION_SYNC_VOLUMES_QUEUE"

awslocal --endpoint-url http://localhost:4566 sns subscribe --topic-arn "arn:aws:sns:us-east-1:000000000000:$TRACKING_EVENTS_TOPIC" --protocol sqs --notification-endpoint "arn:aws:sqs:us-east-1:000000000000:$TRACKING_NOTIFICATION_QUEUE"

awslocal --endpoint-url http://localhost:4566 sns subscribe --topic-arn "arn:aws:sns:us-east-1:000000000000:$CARRIER_TRACKING_CREATED_TOPIC" --protocol sqs --notification-endpoint "arn:aws:sqs:us-east-1:000000000000:$CARRIER_TRACKING_SERVICE_QUEUE"

awslocal --endpoint-url http://localhost:4566 sns subscribe --topic-arn "arn:aws:sns:us-east-1:000000000000:$TRACKING_REQUESTED_SNS_TOPIC" --protocol sqs --notification-endpoint "arn:aws:sqs:us-east-1:000000000000:$CARRIER_TRACKING_SERVICE_UPDATE_REQUEST_QUEUE"