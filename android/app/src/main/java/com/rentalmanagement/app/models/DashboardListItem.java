package com.rentalmanagement.app.models;

public class DashboardListItem {

    private final String title;
    private final String subtitle;
    private final String status;
    private final String meta;

    public DashboardListItem(String title, String subtitle, String status, String meta) {
        this.title = title;
        this.subtitle = subtitle;
        this.status = status;
        this.meta = meta;
    }

    public String getTitle() { return title; }
    public String getSubtitle() { return subtitle; }
    public String getStatus() { return status; }
    public String getMeta() { return meta; }
}
