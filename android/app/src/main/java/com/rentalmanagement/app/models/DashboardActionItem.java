package com.rentalmanagement.app.models;

public class DashboardActionItem {

    private final String title;
    private final String subtitle;
    private final String icon;

    public DashboardActionItem(String title, String subtitle, String icon) {
        this.title = title;
        this.subtitle = subtitle;
        this.icon = icon;
    }

    public String getTitle() { return title; }
    public String getSubtitle() { return subtitle; }
    public String getIcon() { return icon; }
}
