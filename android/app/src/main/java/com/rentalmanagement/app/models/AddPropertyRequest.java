package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class AddPropertyRequest {

    @SerializedName("name")
    private final String name;

    @SerializedName("address")
    private final String address;

    public AddPropertyRequest(String name, String address) {
        this.name = name;
        this.address = address;
    }
}
