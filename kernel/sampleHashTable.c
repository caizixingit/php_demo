#include<stdio.h>
#include<stdlib.h>

typedef struct _Bucket
{
	char* key;
	void* value;
	struct _Bucket* next;
} Bucket;

typedef struct _HashTable
{
	int size;
	int num;
	Bucket** bucket;
} HashTable;

 int hash_map(char* key)
{
	int hash = 0;
	char* tmp = key;

	while(*(tmp++) != '\0')
	{
		hash += *tmp;
	}

	return hash;
}

#define HASH_INDEX(ht, key) (hash_map(key) % (ht)->size)
#define SUCCESS 1
#define FAIL 0


 void hash_if_need_resize(HashTable* ht)
{
	if((ht->size - ht->num) < 1)
	{
		hash_resize(ht);
	}
}

 int hash_resize(HashTable* ht)
{
	int original_size = ht->size;
	ht->size = ht->size * 2;
	ht->num = 0;

	Bucket** new_bucket = (Bucket**)malloc(ht->size * sizeof(Bucket *));
	Bucket** original_bucket = ht->bucket;
	ht->bucket = new_bucket;

	Bucket* bucket = NULL;
	Bucket* tmp = NULL;
	for(int i = 0; i < original_size; i++)
	{
		bucket = original_bucket[i];

		while(bucket)
		{
			hash_add(ht, bucket->key, bucket->value);

			tmp = bucket;
			bucket = bucket->next;
			free(tmp);
		}
	}
	free(original_bucket);

	return SUCCESS;
}


 void hash_init(HashTable* ht, int size)
{
	ht->size = size;
	ht->num = 0;
	ht->bucket = (Bucket**)malloc(sizeof(Bucket) * size);
}

 int hash_add(HashTable* ht, char* key, void* value)
{
	hash_if_need_resize(ht);

	int seq = HASH_INDEX(ht, key);

	Bucket* original_bucket = ht->bucket[seq];
	Bucket* tmp_bucket = original_bucket;

	while(tmp_bucket != NULL)
	{
		while(strcmp(key, tmp_bucket->key) == 0)
		{
				tmp_bucket->value = value;
				return SUCCESS;
		}

		tmp_bucket = tmp_bucket->next;
	}


	Bucket* bucket = (Bucket *)malloc(sizeof(Bucket));
	bucket->key = key;
	bucket->value = value;
	bucket->next = NULL;

	if(original_bucket != NULL)
	{
		bucket->next = original_bucket;
	}

	ht->bucket[seq] = bucket;
	ht->num += 1;

	return SUCCESS;
}

 int hash_get(HashTable* ht, char* key, void** result)
{
	int seq = HASH_INDEX(ht, key);
	Bucket* bucket = ht->bucket[seq];

	if(bucket == NULL)
	{
		return FAIL;
	}

	while(bucket)
	{
		if(strcmp(key, bucket->key) == 0)
		{
			*result = bucket->value;
			return SUCCESS;
		}

		bucket = bucket->next;
	}

	return FAIL;
}

 int hash_delete(HashTable* ht, char* key)
{
	int seq = HASH_INDEX(ht, key);
	Bucket* bucket = ht->bucket[seq];
	Bucket* before = NULL;

	if(bucket == NULL)
	{
		return FAIL;
	}

	while(bucket)
	{
		if(strcmp(key, bucket->key) == 0)
		{
			if(before == NULL)
			{
				ht->bucket[seq] = NULL;
			}
			else
			{
				before->next = bucket->next;
			}

			return SUCCESS;
		}

		before = bucket;
		bucket = bucket->next;
	}

	return FAIL;
}


int main(int argv, char** argc)
{
		printf("0");
	HashTable* ht = (HashTable *)malloc(sizeof(HashTable));
	printf("1");
	hash_init(ht, 2);
	printf("2");
	hash_add(ht, "caizixin", "zixin");
	//hash_add(ht, "caiziwen", "ziwen");
	//hash_add(ht, "caiweihua", "weihua");
printf("3");
	void *name = NULL;
	hash_get(ht, "caizixin", name);
	printf("%s", (char *)name);
}