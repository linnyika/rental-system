package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class AdminLandlordItem {

    @SerializedName("id")
    private Long id;

    @SerializedName("user_id")
    private Long userId;

    @SerializedName("name")
    private String name;

    @SerializedName("email")
    private String email;

    @SerializedName("phone")
    private String phone;

    @SerializedName("properties_count")
    private Integer propertiesCount;

    @SerializedName("created_at")
    private String createdAt;

    public Long getId() {
        return id;
    }

    public Long getUserId() {
        return userId;
    }

    public String getName() {
        return name;
    }

    public String getEmail() {
        return email;
    }

    public String getPhone() {
        return phone;
    }

    public String getCreatedAt() {
        return createdAt;
    }

    public Integer getPropertiesCount() {
        return propertiesCount;
    }
}
