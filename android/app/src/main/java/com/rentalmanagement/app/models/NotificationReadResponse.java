package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class NotificationReadResponse {

    @SerializedName("message")
    private String message;

    @SerializedName("notification")
    private NotificationItem notification;

    public String getMessage() {
        return message;
    }

    public NotificationItem getNotification() {
        return notification;
    }
}
