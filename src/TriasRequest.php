<?php

function sendTriasRequest(string $stopPointRef, string $token, string $url): string {
    $requestTimestamp = (new DateTime())->format('c');

    $requestBody = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Trias version="1.2" xmlns="http://www.vdv.de/trias">
  <ServiceRequest>
    <RequestTimestamp>' . $requestTimestamp . '</RequestTimestamp>
    <RequestorRef>' . $token . '</RequestorRef>
    <RequestPayload>
      <StopEventRequest>
        <Location>
          <LocationRef>
            <StopPlaceRef>' . htmlspecialchars($stopPointRef) . '</StopPlaceRef>
          </LocationRef>
        </Location>
        <Params>
          <StopEventType>departure</StopEventType>
          <IncludeRealtimeData>true</IncludeRealtimeData>
        </Params>
      </StopEventRequest>
    </RequestPayload>
  </ServiceRequest>
</Trias>';

    $headers = [
        'Content-Type: application/xml',
        'Accept: application/xml'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if ($response === false) {
        header('Content-Type: text/plain', true, 500);
        echo "Error:\n- Failed to send request to TRIAS endpoint: " . curl_error($ch);
        exit;
    }

    return $response;
}