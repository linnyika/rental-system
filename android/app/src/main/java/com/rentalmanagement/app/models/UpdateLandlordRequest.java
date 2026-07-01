package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class UpdateLandlordRequest {

    @SerializedName("name")
    private final String name;

    @SerializedName("email")
    private final String email;

    @SerializedName("phone")
    private final String phone;

    public UpdateLandlordRequest(String name, String email, String phone) {
        this.name = name;
        this.email = email;
        this.phone = phone;
    }
}
