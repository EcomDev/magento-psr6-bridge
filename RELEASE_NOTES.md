# Release 1.1.0
New functionality:

* Added normalizer for PSR-6 compatible key. `Psr6Normalizer` uses `EncodeNormalier` and `LengthNormalizer` behind the scenes. 

# Release 1.0.0
Initial features:

* **Encode Normalizer** encodes all characters except alphanum into hex representation 
* **Length Normalizer** strips characters after some length is reached 
* **Scalar Converter** converts any non string scalar to string representation 
* **Hash Converter** converts any array into md5 hash of json encoded value
* **Key Value Converter** converts simple key value pair array into string representation like `key1_value1_key2_value2`
