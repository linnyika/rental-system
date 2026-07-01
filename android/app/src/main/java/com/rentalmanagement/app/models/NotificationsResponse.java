package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class NotificationsResponse {

    @SerializedName("notifications")
    private List<NotificationItem> notifications;

    public List<NotificationItem> getNotifications() {
        return notifications;
    }
}
